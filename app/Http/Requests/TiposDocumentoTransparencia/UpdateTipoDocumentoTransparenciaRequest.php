<?php

namespace App\Http\Requests\TiposDocumentoTransparencia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoDocumentoTransparenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'activo' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
