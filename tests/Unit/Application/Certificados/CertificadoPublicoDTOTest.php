<?php

namespace Tests\Unit\Application\Certificados;

use App\Application\Certificados\DTOs\CertificadoDTO;
use App\Application\Certificados\DTOs\CertificadoPublicoDTO;
use Tests\TestCase;

class CertificadoPublicoDTOTest extends TestCase
{
    private function certificadoCompleto(): CertificadoDTO
    {
        return new CertificadoDTO(
            id: 1,
            lista_aprobado_id: 12,
            plantilla_id: 8,
            usuario_id: 9030,
            imparte_id: 9001,
            nombre_en_certificado: 'Juan Pérez',
            programa_en_certificado: 'Diplomado en Gestión Pública',
            condicion: 'aprobado',
            nota_final: 87.5,
            horas_academicas: 120,
            fecha_inicio_curso: '2026-03-01',
            fecha_fin_curso: '2026-06-30',
            codigo_verificacion: 'cenefco-2026-A4X9K2',
            qr_url: '/storage/certificados/9001/cenefco-2026-A4X9K2_qr.png',
            archivo_url: '/storage/certificados/9001/cenefco-2026-A4X9K2.pdf',
            archivo_miniatura_url: '/storage/certificados/9001/cenefco-2026-A4X9K2_preview.jpg',
            estado: 'generado',
            motivo_anulacion: null,
            anulado_por: null,
            created_at: '2026-07-25T10:00:00Z',
            updated_at: '2026-07-25T10:00:00Z',
        );
    }

    public function test_no_expone_ids_ni_campos_internos_de_staff(): void
    {
        $publico = json_decode(json_encode(CertificadoPublicoDTO::fromDTO($this->certificadoCompleto())), true);

        foreach (['id', 'lista_aprobado_id', 'plantilla_id', 'usuario_id', 'imparte_id', 'motivo_anulacion', 'anulado_por', 'updated_at'] as $campoInterno) {
            $this->assertArrayNotHasKey($campoInterno, $publico);
        }
    }

    public function test_conserva_los_campos_que_la_verificacion_publica_necesita(): void
    {
        $publico = CertificadoPublicoDTO::fromDTO($this->certificadoCompleto());

        $this->assertSame('cenefco-2026-A4X9K2', $publico->codigo_verificacion);
        $this->assertSame('Juan Pérez', $publico->nombre_en_certificado);
        $this->assertSame('Diplomado en Gestión Pública', $publico->programa_en_certificado);
        $this->assertSame('generado', $publico->estado);
        $this->assertSame(87.5, $publico->nota_final);
    }
}
