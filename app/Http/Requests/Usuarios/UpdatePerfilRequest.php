<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['sometimes', 'string', 'max:100'],
            'apellido'         => ['sometimes', 'string', 'max:100'],
            'ci'               => ['sometimes', 'nullable', 'string', 'max:30'],
            'telefono'         => ['sometimes', 'nullable', 'string', 'max:20'],
            'current_password' => ['required_with:password', 'string'],
            'password'         => ['sometimes', 'string', 'min:8', 'confirmed'],
        ];
    }
}