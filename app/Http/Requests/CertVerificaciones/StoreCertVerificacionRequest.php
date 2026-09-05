<?php

namespace App\Http\Requests\CertVerificaciones;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertVerificacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'certificado_id'    => ['nullable', 'integer'],
            'codigo_consultado' => ['required', 'string', 'max:100'],
            'resultado'         => ['required', 'string', 'max:20'],
            'ip_origen'         => ['nullable', 'string', 'max:45'],
            'user_agent'        => ['nullable', 'string', 'max:500'],
            'pais'              => ['nullable', 'string', 'max:100'],
        ];
    }
}
