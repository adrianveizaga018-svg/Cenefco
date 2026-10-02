<?php

namespace App\Application\CampanasLeads\Services;

use App\Application\CampanasLeads\DTOs\ImportarLeadsResultDTO;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportarLeadsExcelService
{
    private const ALIAS_COLUMNAS = [
        'nombre'    => ['nombre', 'nombres', 'nombre completo', 'nombre(s)'],
        'celular'   => ['celular', 'telefono', 'teléfono', 'whatsapp', 'numero', 'número', 'nro celular', 'celular/whatsapp'],
        'correo'    => ['correo', 'email', 'correo electronico', 'correo electrónico', 'e-mail'],
        'profesion' => ['profesion', 'profesión', 'ocupacion', 'ocupación', 'carrera'],
    ];

    public function __construct(private readonly LeadRepositoryInterface $repository) {}

    public static function reglas(): array
    {
        return [
            'nombre'    => ['required', 'string', 'max:150'],
            'celular'   => ['required', 'string', 'max:30'],
            'correo'    => ['nullable', 'email', 'max:150'],
            'profesion' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function importar(int $campanaLeadId, string $rutaArchivo, string $extension): ImportarLeadsResultDTO
    {
        $readerType = match (strtolower($extension)) {
            'xlsx'  => 'Xlsx',
            'xls'   => 'Xls',
            default => 'Csv',
        };

        $spreadsheet = IOFactory::createReader($readerType)->load($rutaArchivo);
        $filas       = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($filas)) {
            return new ImportarLeadsResultDTO(0, 0, ['El archivo está vacío.']);
        }

        $columnas = $this->mapearColumnas($filas[0] ?? []);
        if (! isset($columnas['nombre']) || ! isset($columnas['celular'])) {
            return new ImportarLeadsResultDTO(0, 0, [
                'No se pudo identificar las columnas del archivo. La primera fila debe tener encabezados '
                .'con al menos "Nombre" y "Celular" (opcionalmente también: Correo, Profesión).',
            ]);
        }
        array_shift($filas);

        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];

        foreach ($filas as $i => $fila) {
            $numeroFila = $i + 2;

            $celdasConValor = array_filter($fila, fn ($v) => $v !== null && trim((string) $v) !== '');
            if (empty($celdasConValor)) {
                continue;
            }

            $datosFila = [
                'nombre'    => $this->celda($fila, $columnas, 'nombre'),
                'celular'   => $this->celda($fila, $columnas, 'celular'),
                'correo'    => $this->celda($fila, $columnas, 'correo'),
                'profesion' => $this->celda($fila, $columnas, 'profesion'),
            ];

            $validator = Validator::make($datosFila, self::reglas());
            if ($validator->fails()) {
                $errores[] = "Fila {$numeroFila}: ".implode(' ', $validator->errors()->all());
                $omitidos++;
                continue;
            }

            try {
                $this->repository->create($campanaLeadId, $validator->validated());
                $insertados++;
            } catch (\Throwable $e) {
                $errores[] = "Fila {$numeroFila}: error inesperado al registrar — ".$e->getMessage();
                $omitidos++;
            }
        }

        return new ImportarLeadsResultDTO($insertados, $omitidos, $errores);
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
}
