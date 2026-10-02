<?php

namespace App\Http\Requests\Permisos;

use Illuminate\Foundation\Http\FormRequest;

class StorePermisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:100|unique:permisos,codigo',
            'descripcion' => 'nullable|string|max:150',
            'modulo' => 'nullable|string|max:50',
        ];
    }
}
