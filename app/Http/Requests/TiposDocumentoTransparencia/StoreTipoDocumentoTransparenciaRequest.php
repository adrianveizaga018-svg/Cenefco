<?php

namespace App\Http\Requests\TiposDocumentoTransparencia;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDocumentoTransparenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
