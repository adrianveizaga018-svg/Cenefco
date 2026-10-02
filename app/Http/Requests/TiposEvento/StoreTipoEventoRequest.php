<?php

namespace App\Http\Requests\TiposEvento;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'color_hex' => ['nullable', 'string', 'max:50'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
