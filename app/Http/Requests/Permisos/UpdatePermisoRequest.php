<?php

namespace App\Http\Requests\Permisos;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'sometimes|string|max:100|unique:permisos,codigo,'.$this->route('id'),
            'descripcion' => 'nullable|string|max:150',
            'modulo' => 'nullable|string|max:50',
        ];
    }
}
