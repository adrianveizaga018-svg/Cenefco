<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaCampoSeeder extends Seeder
{
    private array $ciudades = [
        'La Paz', 'Santa Cruz', 'Cochabamba', 'Potosí',
        'Tarija', 'Oruro', 'Sucre', 'Beni', 'Pando', 'Otra',
    ];

    private array $mediosPago = [
        'Depósito', 'Transferencia', 'QR',
        'Tigo Money', 'Pago Personal en Oficinas',
    ];

    private array $expedidos = [
        ['id' => 1, 'nombre' => 'Beni'],
        ['id' => 2, 'nombre' => 'Chuquisaca'],
        ['id' => 3, 'nombre' => 'Cochabamba'],
        ['id' => 4, 'nombre' => 'La Paz'],
        ['id' => 5, 'nombre' => 'Oruro'],
        ['id' => 6, 'nombre' => 'Pando'],
        ['id' => 7, 'nombre' => 'Potosí'],
        ['id' => 8, 'nombre' => 'Santa Cruz'],
        ['id' => 9, 'nombre' => 'Tarija'],
        ['id' => 10, 'nombre' => 'Extranjero'],
    ];

    public function run(): void
    {
        DB::table('web_categoria_campo')->truncate();

        $campos = array_merge(
            $this->camposDiplomado(),
            $this->camposCurso(),
            $this->camposTaller(),
            $this->camposMaestria(),
            $this->camposEspecializacion(),
            $this->camposSeminario(),
            $this->camposCertificacion(),
        );

        $now = now()->toDateTimeString();
        foreach ($campos as &$campo) {
            $campo['activo']     = true;
            $campo['created_at'] = $now;
            $campo['updated_at'] = $now;
        }

        DB::table('web_categoria_campo')->insert($campos);

        $total = DB::table('web_categoria_campo')->count();
        $this->command->info("✓ {$total} campos insertados en web_categoria_campo.");
    }

    private function campo(int $cat, int $paso, string $nombre, string $etiqueta, string $tipo, bool $req, int $orden, string $ayuda = '', ?array $opciones = null, ?array $validacion = null): array
    {
        return [
            'categoria_id' => $cat,
            'paso'         => $paso,
            'nombre_campo' => $nombre,
            'etiqueta'     => $etiqueta,
            'tipo_campo'   => $tipo,
            'requerido'    => $req,
            'orden'        => $orden,
            'ayuda'        => $ayuda,
            'opciones'     => $opciones !== null ? json_encode($opciones) : null,
            'validacion'   => $validacion !== null ? json_encode($validacion) : null,
        ];
    }

    private function datosPersonalesCompletos(int $cat): array
    {
        return [
            $this->campo($cat, 1, 'email',            'Correo electrónico',                    'email',  true,  1, 'Usaremos este correo para enviarte la confirmación.'),
            $this->campo($cat, 1, 'nombre',            'Nombre(s)',                             'text',   true,  2, 'Tus nombres tal como aparecen en tu CI.'),
            $this->campo($cat, 1, 'apellido_paterno',  'Apellido Paterno',                      'text',   false, 3),
            $this->campo($cat, 1, 'apellido_materno',  'Apellido Materno',                      'text',   false, 4),
            $this->campo($cat, 1, 'fecha_nacimiento',  'Fecha de nacimiento',                   'date',   true,  5),
            $this->campo($cat, 1, 'ci',                'Carnet de Identidad',                   'text',   true,  6, 'Solo el número, sin extensión.'),
            $this->campo($cat, 1, 'expedido_id',       'Extensión (departamento del CI)',        'select', false, 7, '', $this->expedidos),
            $this->campo($cat, 1, 'telefono',          'Teléfono celular (grupo de INSCRITOS)', 'text',   true,  8, 'Número donde recibirás el enlace al grupo de WhatsApp.'),
        ];
    }

    private function datosPersonalesBasicos(int $cat): array
    {
        return [
            $this->campo($cat, 1, 'email',    'Correo electrónico',   'email', true,  1, 'Para enviarte la confirmación y el certificado.'),
            $this->campo($cat, 1, 'nombre',   'Nombre completo',      'text',  true,  2, 'Nombre y apellidos tal como deseas que aparezcan en el certificado.'),
            $this->campo($cat, 1, 'ci',       'Carnet de Identidad',  'text',  true,  3, 'Solo el número, sin extensión.'),
            $this->campo($cat, 1, 'telefono', 'Teléfono celular',     'text',  true,  4),
        ];
    }

    private function filePdf(int $cat, string $nombre, string $etiqueta, bool $req, int $orden, string $ayuda, string $accept = '.pdf,.jpg,.jpeg,.png', int $maxMb = 10): array
    {
        return $this->campo($cat, 2, $nombre, $etiqueta, 'file_pdf', $req, $orden, $ayuda, null, ['max_mb' => $maxMb, 'accept' => $accept]);
    }

    private function fileImg(int $cat, string $nombre, string $etiqueta, bool $req, int $orden, string $ayuda): array
    {
        return $this->campo($cat, 2, $nombre, $etiqueta, 'file_image', $req, $orden, $ayuda, null, ['max_mb' => 5, 'accept' => '.jpg,.jpeg,.png']);
    }

    private function ciudadField(int $cat, int $orden, bool $req = true): array
    {
        return $this->campo($cat, 2, 'ciudad', 'Ciudad de residencia', 'select', $req, $orden, 'Para la entrega del certificado físico.', $this->ciudades);
    }

    private function provinciaField(int $cat, int $orden): array
    {
        return $this->campo($cat, 2, 'provincia', 'Provincia (si no vives en capital)', 'text', false, $orden, 'Especifica tu provincia si no estás en la ciudad capital.');
    }

    private function medioPagoField(int $cat, int $orden): array
    {
        return $this->campo($cat, 2, 'medio_pago', 'Medio de pago utilizado', 'select', true, $orden, 'Selecciona cómo realizaste el pago.', $this->mediosPago);
    }

    private function montoPagadoField(int $cat, int $orden): array
    {
        return $this->campo($cat, 2, 'monto_pagado', 'Monto pagado (Bs.)', 'number', true, $orden, 'Ingresa el monto exacto que depositaste o transferiste.');
    }

    private function sugerenciaCursoField(int $cat, int $orden): array
    {
        return $this->campo($cat, 2, 'sugerencia_curso', '¿Podría sugerirnos algún curso de su interés?', 'text', false, $orden, 'Tu opinión nos ayuda a diseñar nuevos programas.');
    }

    private function recomendarDocenteField(int $cat, int $orden): array
    {
        return $this->campo($cat, 2, 'recomendar_docente', '¿Desea recomendar a un docente experto?', 'boolean', false, $orden, 'Si marca Sí, detalle el nombre, especialidad y contacto en el campo siguiente.');
    }

    private function detalleDocenteField(int $cat, int $orden): array
    {
        return $this->campo($cat, 2, 'detalle_docente', 'Datos del docente recomendado', 'textarea', false, $orden, 'Nombre completo, especialidad y número de contacto del docente.');
    }

    private function camposDiplomado(): array
    {
        $c = 1;
        return array_merge(
            $this->datosPersonalesCompletos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso', 'Carnet de Identidad (anverso)',                true,  1, 'Foto o escaneo del anverso de tu CI. PDF, JPG o PNG — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_ci_reverso', 'Carnet de Identidad (reverso)',                true,  2, 'Foto o escaneo del reverso de tu CI. PDF, JPG o PNG — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_titulo',      'Título de Licenciatura en Provisión Nacional', true,  3, 'Requisito para la emisión del diploma avalado por la USFA. PDF — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_cv',          'Currículum Vitae (CV)',                        true,  4, 'Requisito para el registro académico. PDF o DOC — máx. 10 MB.', '.pdf,.doc,.docx'),
                $this->fileImg($c, 'archivo_foto_3x3',    'Fotografía 3×3 fondo azul mate',               true,  5, 'Fotografía de estudio. JPG o PNG — máx. 5 MB.'),
                $this->campo($c, 2, 'grado_academico', 'Grado académico obtenido', 'select', true, 6, 'Selecciona el grado de tu título.', ['Técnico Superior', 'Licenciatura', 'Maestría', 'Doctorado', 'Otro']),
                $this->ciudadField($c, 7),
                $this->provinciaField($c, 8),
                $this->medioPagoField($c, 9),
                $this->montoPagadoField($c, 10),
                $this->sugerenciaCursoField($c, 11),
                $this->recomendarDocenteField($c, 12),
                $this->detalleDocenteField($c, 13),
            ]
        );
    }

    private function camposCurso(): array
    {
        $c = 2;
        return array_merge(
            $this->datosPersonalesCompletos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso', 'Carnet de Identidad (foto o escaneo)', true, 1, 'Foto o escaneo de tu CI. PDF, JPG o PNG — máx. 5 MB.', '.pdf,.jpg,.jpeg,.png', 5),
                $this->ciudadField($c, 2),
                $this->medioPagoField($c, 3),
                $this->montoPagadoField($c, 4),
                $this->sugerenciaCursoField($c, 5),
            ]
        );
    }

    private function camposTaller(): array
    {
        $c = 3;
        return array_merge(
            $this->datosPersonalesBasicos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso',  'Carnet de Identidad (opcional)', false, 1, 'Adjunta tu CI si deseas que aparezca en el certificado. PDF, JPG o PNG — máx. 5 MB.', '.pdf,.jpg,.jpeg,.png', 5),
                $this->campo($c, 2, 'institucion_empresa', 'Institución o empresa donde trabaja', 'text', false, 2, 'Nombre de tu institución o empresa (opcional).'),
                $this->ciudadField($c, 3, false),
            ]
        );
    }

    private function camposMaestria(): array
    {
        $c = 4;
        return array_merge(
            $this->datosPersonalesCompletos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso',       'Carnet de Identidad (anverso)',                true,  1, 'PDF, JPG o PNG — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_ci_reverso',       'Carnet de Identidad (reverso)',                true,  2, 'PDF, JPG o PNG — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_titulo',            'Título de Licenciatura en Provisión Nacional', true,  3, 'PDF — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_cv',                'Currículum Vitae',                             true,  4, 'PDF o DOC — máx. 10 MB.', '.pdf,.doc,.docx'),
                $this->filePdf($c, 'archivo_carta_motivacion',  'Carta de motivación',                          true,  5, 'Explica por qué deseas cursar esta maestría. PDF — máx. 5 MB.', '.pdf', 5),
                $this->fileImg($c, 'archivo_foto_3x3',          'Fotografía 3×3 fondo azul mate',               true,  6, 'Fotografía de estudio. JPG o PNG — máx. 5 MB.'),
                $this->campo($c, 2, 'anios_experiencia', 'Años de experiencia profesional', 'number', true, 7, 'Mínimo 2 años requeridos.'),
                $this->ciudadField($c, 8),
                $this->provinciaField($c, 9),
                $this->medioPagoField($c, 10),
                $this->montoPagadoField($c, 11),
                $this->sugerenciaCursoField($c, 12),
                $this->recomendarDocenteField($c, 13),
                $this->detalleDocenteField($c, 14),
            ]
        );
    }

    private function camposEspecializacion(): array
    {
        $c = 5;
        return array_merge(
            $this->datosPersonalesCompletos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso',         'Carnet de Identidad (anverso)',          true,  1, 'PDF, JPG o PNG — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_titulo',              'Título académico (Licenciatura o superior)', true, 2, 'PDF — máx. 10 MB.'),
                $this->filePdf($c, 'archivo_constancia_laboral',  'Constancia de trabajo (opcional)',       false, 3, 'Documento emitido por tu empleador. PDF — máx. 5 MB.', '.pdf', 5),
                $this->fileImg($c, 'archivo_foto_3x3',            'Fotografía 3×3 fondo azul mate',         true,  4, 'Fotografía de estudio. JPG o PNG — máx. 5 MB.'),
                $this->ciudadField($c, 5),
                $this->provinciaField($c, 6),
                $this->medioPagoField($c, 7),
                $this->montoPagadoField($c, 8),
            ]
        );
    }

    private function camposSeminario(): array
    {
        $c = 6;
        return array_merge(
            $this->datosPersonalesBasicos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso', 'Carnet de Identidad', true, 1, 'Necesario para emitir el certificado de asistencia. PDF, JPG o PNG — máx. 5 MB.', '.pdf,.jpg,.jpeg,.png', 5),
                $this->campo($c, 2, 'area_profesional', 'Área o profesión', 'select', false, 2, 'Selecciona tu área de desempeño.', [
                    'Administración y Negocios', 'Derecho y Ciencias Jurídicas', 'Educación',
                    'Ingeniería y Tecnología', 'Salud y Medicina', 'Ciencias Sociales',
                    'Contabilidad y Finanzas', 'Otra',
                ]),
                $this->ciudadField($c, 3),
                $this->medioPagoField($c, 4),
                $this->montoPagadoField($c, 5),
            ]
        );
    }

    private function camposCertificacion(): array
    {
        $c = 7;
        return array_merge(
            $this->datosPersonalesCompletos($c),
            [
                $this->filePdf($c, 'archivo_ci_anverso',          'Carnet de Identidad (anverso)',        true,  1, 'PDF, JPG o PNG — máx. 5 MB.', '.pdf,.jpg,.jpeg,.png', 5),
                $this->filePdf($c, 'archivo_evidencia_experiencia', 'Evidencia de experiencia (opcional)', false, 2, 'Diploma, certificado previo o constancia laboral. PDF — máx. 10 MB.'),
                $this->campo($c, 2, 'nivel_conocimiento', 'Nivel de conocimiento previo en el área', 'select', true, 3, '¿Cuánto sabes del tema antes de certificarte?', ['Ninguno', 'Básico', 'Intermedio', 'Avanzado']),
                $this->ciudadField($c, 4),
                $this->medioPagoField($c, 5),
                $this->montoPagadoField($c, 6),
            ]
        );
    }
}
