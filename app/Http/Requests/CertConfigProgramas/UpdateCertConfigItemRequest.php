<?php

namespace App\Http\Requests\CertConfigProgramas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertConfigItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'plantilla_id' => ['nullable', 'integer', 'exists:t_cert_plantilla,id'],
            'nombre_cert'  => ['required', 'string', 'max:300'],
            'precio'       => ['required', 'numeric', 'min:0'],
            'es_gratuito'  => ['boolean'],
            'orden'        => ['integer', 'min:0'],
            'activo'       => ['boolean'],
        ];
    }
}
