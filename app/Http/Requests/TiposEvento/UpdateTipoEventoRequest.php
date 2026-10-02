<?php

namespace App\Http\Requests\TiposEvento;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'color_hex' => ['nullable', 'string', 'max:50'],
            'activo' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
