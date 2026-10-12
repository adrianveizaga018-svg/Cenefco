<?php

namespace App\Application\Crm\Services;

use App\Infrastructure\CampanasLeads\Models\Lead;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Carga masiva de contactos al embudo (los de un grupo de WhatsApp, una feria, una base).
 * Cada fila pasa por CrmService::registrar: no duplica, reconoce a quien ya es estudiante
 * y reparte por turnos (o a un vendedor fijo).
 */
class ImportarContactosCrmService
{
    private const ALIAS = [
        'nombre'  => ['nombre', 'nombres', 'nombre completo', 'nombre(s)', 'contacto', 'name'],
        'celular' => ['celular', 'telefono', 'whatsapp', 'numero', 'nro celular', 'celular/whatsapp', 'phone', 'movil'],
        'correo'  => ['correo', 'email', 'correo electronico', 'e-mail'],
        'ci'      => ['ci', 'carnet', 'cedula', 'c.i.', 'documento'],
    ];

    public const MAX_FILAS = 2000;

    public function __construct(private readonly CrmService $crm) {}

    /**
     * @param array{origen: string, vendedor_id?: ?int, programa_id?: ?int, campana_publicidad_id?: ?int, nota?: ?string} $opciones
     * @return array{nuevos: int, ya_estaban: int, ya_estudiantes: int, errores: array<int, string>, por_vendedor: array<string, int>}
     */
    public function importar(string $ruta, string $extension, array $opciones): array
    {
        $lector = IOFactory::createReader(match (strtolower($extension)) { 'xlsx' => 'Xlsx', 'xls' => 'Xls', default => 'Csv' });
        $filas = $lector->load($ruta)->getActiveSheet()->toArray(null, true, true, false);

        $columnas = $this->columnas($filas[0] ?? []);
        $conEncabezado = $columnas !== [];
        if (! $conEncabezado) {
            // Lista pegada sin encabezado (lo típico al copiar los números de un grupo): primera columna = celular
            $columnas = ['celular' => 0];
        } else {
            array_shift($filas);
        }
        if (! isset($columnas['celular']) && ! isset($columnas['correo'])) {
            return $this->resultado(errores: ['No encontramos una columna de celular o correo. Usa la plantilla o pon "Celular" en la primera fila.']);
        }
        if (count($filas) > self::MAX_FILAS) {
            return $this->resultado(errores: ['El archivo tiene más de '.self::MAX_FILAS.' filas. Divídelo en partes.']);
        }

        $ultimoId = (int) (Lead::withTrashed()->max('id') ?? 0);
        $nuevos = $yaEstaban = $yaEstudiantes = 0;
        $errores = [];
        $porVendedor = [];

        foreach ($filas as $i => $fila) {
            $valor = fn (string $campo) => isset($columnas[$campo]) ? (trim((string) ($fila[$columnas[$campo]] ?? '')) ?: null) : null;
            $datos = [
                'nombre'  => $valor('nombre'),
                'celular' => $valor('celular'),
                'correo'  => $valor('correo'),
                'ci'      => $valor('ci'),
            ];
            if (! array_filter($datos)) {
                continue; // fila vacía
            }
            $nFila = $i + ($conEncabezado ? 2 : 1);
            if ($datos['correo'] && ! filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                $datos['correo'] = null;
            }
            $celular = CrmService::normalizarCelular($datos['celular']);
            if ($datos['celular'] && (! $celular || strlen($celular) < 7 || strlen($celular) > 15)) {
                $errores[] = "Fila {$nFila}: el número \"{$datos['celular']}\" no parece un celular.";
                continue;
            }

            try {
                $lead = $this->crm->registrar($datos + [
                    'vendedor_id'           => $opciones['vendedor_id'] ?? null,
                    'programa_id'           => $opciones['programa_id'] ?? null,
                    'campana_publicidad_id' => $opciones['campana_publicidad_id'] ?? null,
                    'nota'                  => $opciones['nota'] ?? null,
                ], $opciones['origen']);
            } catch (\Throwable $e) {
                $errores[] = "Fila {$nFila}: ".$e->getMessage();
                continue;
            }

            if ($lead->id > $ultimoId) {
                $nuevos++;
                if ($lead->id_us) {
                    $yaEstudiantes++;
                }
                $vendedor = $lead->vendedor_asignado_id ? (string) $lead->vendedor_asignado_id : 'sin';
                $porVendedor[$vendedor] = ($porVendedor[$vendedor] ?? 0) + 1;
            } else {
                $yaEstaban++;
            }
        }

        return $this->resultado($nuevos, $yaEstaban, $yaEstudiantes, $errores, $porVendedor);
    }

    /** Plantilla CSV que Excel abre directo. */
    public static function plantillaCsv(): string
    {
        return "\u{FEFF}Nombre,Celular,Correo,CI\nMaría Quispe,71234567,maria@ejemplo.com,\nJorge Mamani,+591 76543210,,1234567\n";
    }

    private function columnas(array $encabezado): array
    {
        $columnas = [];
        foreach ($encabezado as $indice => $texto) {
            $texto = preg_replace('/\s+/', ' ', strtr(mb_strtolower(trim((string) $texto)), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n']));
            foreach (self::ALIAS as $campo => $alias) {
                if (! isset($columnas[$campo]) && in_array($texto, $alias, true)) {
                    $columnas[$campo] = $indice;
                }
            }
        }

        return $columnas;
    }

    private function resultado(int $nuevos = 0, int $yaEstaban = 0, int $yaEstudiantes = 0, array $errores = [], array $porVendedor = []): array
    {
        return [
            'nuevos'         => $nuevos,
            'ya_estaban'     => $yaEstaban,
            'ya_estudiantes' => $yaEstudiantes,
            'errores'        => array_slice($errores, 0, 50),
            'por_vendedor'   => $porVendedor,
        ];
    }
}
