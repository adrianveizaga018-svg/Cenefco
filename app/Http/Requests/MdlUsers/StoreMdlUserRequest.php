<?php

namespace App\Http\Requests\MdlUsers;

use Illuminate\Foundation\Http\FormRequest;

class StoreMdlUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id'             => 'required|integer',
            'num_modusuario' => 'nullable|integer',
            'nombre_usuario' => 'nullable|string|max:100',
            'nombre'         => 'nullable|string|max:100',
            'appaterno'      => 'nullable|string|max:100',
            'apmaterno'      => 'nullable|string|max:200',
            'ci'             => 'nullable|string|max:200',
            'expedido'       => 'nullable|integer',
            'telefono'       => 'nullable|string|max:20',
            'celular'        => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'direccion'      => 'nullable|string|max:255',
            'ciudad'         => 'nullable|string|max:120',
            'per_modificar'  => 'nullable|integer',
        ];
    }
}
