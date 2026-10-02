<?php

namespace App\Http\Requests\Certificados;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_en_certificado'   => ['sometimes', 'required', 'string', 'max:300'],
            'programa_en_certificado' => ['sometimes', 'required', 'string', 'max:300'],
            'qr_url'                  => ['nullable', 'string', 'max:500'],
            'archivo_url'             => ['nullable', 'string', 'max:500'],
            'archivo_miniatura_url'   => ['nullable', 'string', 'max:255'],
            'estado'                  => ['nullable', 'string', 'max:50'],
            'motivo_anulacion'        => ['nullable', 'string'],
            'anulado_por'             => ['nullable', 'integer'],
        ];
    }
}
