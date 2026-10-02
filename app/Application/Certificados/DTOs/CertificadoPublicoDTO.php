<?php

namespace App\Application\Certificados\DTOs;

final readonly class CertificadoPublicoDTO
{
    public function __construct(
        public string  $codigo_verificacion,
        public string  $nombre_en_certificado,
        public string  $programa_en_certificado,
        public string  $condicion,
        public ?float  $nota_final,
        public ?int    $horas_academicas,
        public ?string $fecha_inicio_curso,
        public ?string $fecha_fin_curso,
        public ?string $qr_url,
        public ?string $archivo_url,
        public ?string $archivo_miniatura_url,
        public string  $estado,
        public ?string $created_at,
    ) {}

    public static function fromDTO(CertificadoDTO $dto): self
    {
        return new self(
            codigo_verificacion:     $dto->codigo_verificacion,
            nombre_en_certificado:   $dto->nombre_en_certificado,
            programa_en_certificado: $dto->programa_en_certificado,
            condicion:               $dto->condicion,
            nota_final:              $dto->nota_final,
            horas_academicas:        $dto->horas_academicas,
            fecha_inicio_curso:      $dto->fecha_inicio_curso,
            fecha_fin_curso:         $dto->fecha_fin_curso,
            qr_url:                  $dto->qr_url,
            archivo_url:             $dto->archivo_url,
            archivo_miniatura_url:   $dto->archivo_miniatura_url,
            estado:                  $dto->estado,
            created_at:              $dto->created_at,
        );
    }
}
