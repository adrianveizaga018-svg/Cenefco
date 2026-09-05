<?php

namespace App\Http\Requests\TiposNorma;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoNormaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'sigla' => ['nullable', 'string', 'max:80'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'activo' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
