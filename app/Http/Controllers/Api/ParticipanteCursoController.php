<?php

namespace App\Http\Controllers\Api;

use App\Application\Participantes\Services\RegistrarParticipanteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParticipanteCursoController extends Controller
{
    private const ALIAS_COLUMNAS = [
        'ci'          => ['ci', 'cedula', 'cedula de identidad', 'carnet', 'carnet de identidad', 'nro documento', 'documento', 'nro ci'],
        'nombre'      => ['nombre', 'nombres', 'nombre(s)'],
        'appaterno'   => ['apellido paterno', 'ap paterno', 'appaterno', 'primer apellido', 'paterno'],
        'apmaterno'   => ['apellido materno', 'ap materno', 'apmaterno', 'segundo apellido', 'materno'],
        'email'       => ['email', 'correo', 'correo electronico', 'e-mail'],
        'condicion'   => ['condicion', 'estado'],
        'nota_final'  => ['nota', 'nota final', 'calificacion'],
        'observacion' => ['observacion', 'comentario', 'comentarios'],
    ];

    public function __construct(
        private readonly RegistrarParticipanteService $registrador,
    ) {}

    public function registrar(Request $request): JsonResponse
    {
        $soloNombreCompleto = $request->boolean('solo_nombre_completo');

        $data = $request->validate([
            'imparte_id'           => ['required', 'integer', 'min:1', $this->reglaImparteExiste()],
            'comprobante_url'      => ['nullable', 'string', 'max:500'],
            'solo_nombre_completo' => ['nullable', 'boolean'],
            ...RegistrarParticipanteService::reglas($soloNombreCompleto),
        ]);

        $aprobado = $this->registrador->registrar((int) $data['imparte_id'], $data);

        return response()->json($this->filaConUsuario($aprobado->id), 201);
    }

    public function importarExcel(Request $request): JsonResponse
    {
        $data = $request->validate([
            'imparte_id'            => ['required', 'integer', 'min:1', $this->reglaImparteExiste()],
            'archivo'               => ['required', 'file', 'max:5120',
                'mimes:xlsx,xls,csv,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'solo_nombre_completo'  => ['nullable', 'boolean'],
        ]);

        $imparteId          = (int) $data['imparte_id'];
        $soloNombreCompleto = $request->boolean('solo_nombre_completo');
        $extension          = strtolower($request->file('archivo')->getClientOriginalExtension());

        try {
            $readerType  = match ($extension) {
                'xlsx'  => 'Xlsx',
                'xls'   => 'Xls',
                default => 'Csv',
            };
            $spreadsheet = IOFactory::createReader($readerType)->load($request->file('archivo')->getRealPath());
            $filas       = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo leer el archivo: '.$e->getMessage()], 422);
        }

        if (empty($filas)) {
            return response()->json(['message' => 'El archivo está vacío.'], 422);
        }

        if ($soloNombreCompleto) {
            $columnas = null;
        } else {
            $columnas = $this->mapearColumnas($filas[0] ?? []);
            if (! isset($columnas['ci']) || ! isset($columnas['nombre'])) {
                return response()->json([
                    'message' => 'No se pudo identificar las columnas del archivo. La primera fila debe tener encabezados '
                        .'con al menos "CI" y "Nombre" (opcionalmente también: Apellido Paterno, Apellido Materno, '
                        .'Email, Condición, Nota Final, Observación).',
                ], 422);
            }
            array_shift($filas);
        }

        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];
        $registrados = [];

        foreach ($filas as $i => $fila) {
            $numeroFila = $i + 1;

            $celdasConValor = array_filter($fila, fn ($v) => $v !== null && trim((string) $v) !== '');
            if (empty($celdasConValor)) {
                continue;
            }

            $datosFila = $soloNombreCompleto
                ? [
                    'ci'          => null,
                    'nombre'      => trim((string) ($fila[0] ?? '')) ?: null,
                    'appaterno'   => null,
                    'apmaterno'   => null,
                    'email'       => null,
                    'condicion'   => null,
                    'nota_final'  => null,
                    'observacion' => null,
                ]
                : [
                    'ci'          => $this->celda($fila, $columnas, 'ci'),
                    'nombre'      => $this->celda($fila, $columnas, 'nombre'),
                    'appaterno'   => $this->celda($fila, $columnas, 'appaterno'),
                    'apmaterno'   => $this->celda($fila, $columnas, 'apmaterno'),
                    'email'       => $this->celda($fila, $columnas, 'email'),
                    'condicion'   => mb_strtolower((string) ($this->celda($fila, $columnas, 'condicion') ?? '')) ?: null,
                    'nota_final'  => $this->celda($fila, $columnas, 'nota_final'),
                    'observacion' => $this->celda($fila, $columnas, 'observacion'),
                ];

            $validator = Validator::make($datosFila, RegistrarParticipanteService::reglas($soloNombreCompleto));
            if ($validator->fails()) {
                $errores[] = "Fila {$numeroFila}: ".implode(' ', $validator->errors()->all());
                $omitidos++;
                continue;
            }

            try {
                $datosValidados = $validator->validated();
                $this->registrador->registrar($imparteId, $datosValidados);
                $insertados++;
                $registrados[] = trim(implode(' ', array_filter([
                    $datosValidados['appaterno'] ?? null,
                    $datosValidados['apmaterno'] ?? null,
                    $datosValidados['nombre'],
                ])));
            } catch (ValidationException $e) {
                $errores[] = "Fila {$numeroFila}: ".implode(' ', Arr::flatten($e->errors()));
                $omitidos++;
            } catch (\Throwable $e) {
                $errores[] = "Fila {$numeroFila}: error inesperado al registrar — ".$e->getMessage();
                $omitidos++;
            }
        }

        return response()->json([
            'insertados'  => $insertados,
            'registrados' => $registrados,
            'omitidos'   => $omitidos,
            'errores'    => $errores,
        ]);
    }

    private function reglaImparteExiste(): Exists
    {

        return Rule::exists('t_imparte', 'id_imp');
    }

    private function mapearColumnas(array $encabezado): array
    {
        $columnas = [];
        foreach ($encabezado as $indice => $valor) {
            $texto = $this->normalizarTexto((string) ($valor ?? ''));
            if ($texto === '') {
                continue;
            }
            foreach (self::ALIAS_COLUMNAS as $campo => $alias) {
                if (! isset($columnas[$campo]) && in_array($texto, $alias, true)) {
                    $columnas[$campo] = $indice;
                    break;
                }
            }
        }

        return $columnas;
    }

    private function celda(array $fila, array $columnas, string $campo): ?string
    {
        if (! isset($columnas[$campo])) {
            return null;
        }
        $valor = trim((string) ($fila[$columnas[$campo]] ?? ''));

        return $valor !== '' ? $valor : null;
    }

    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));
        $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n']);

        return preg_replace('/\s+/', ' ', $texto) ?? $texto;
    }

    private function filaConUsuario(int $listaAprobadosId): ?object
    {
        return DB::table('t_lista_aprobados as la')
            ->leftJoin('t_usuario as us', fn ($j) => $j->on('us.id_us', '=', 'la.usuario_id')->where('us.id_us_reg', 0))
            ->where('la.id', $listaAprobadosId)
            ->select([
                'la.id', 'la.imparte_id', 'la.usuario_id', 'la.condicion',
                'la.nota_final', 'la.estado_certificado', 'la.comprobante_url', 'la.created_at',
                DB::raw("TRIM(CONCAT_WS(' ', NULLIF(us.appaterno,''), NULLIF(us.apmaterno,''), NULLIF(us.nombre,''))) as estudiante_nombre"),
                'us.ci as estudiante_ci',
                'us.email as estudiante_email',
            ])
            ->first();
    }
}
