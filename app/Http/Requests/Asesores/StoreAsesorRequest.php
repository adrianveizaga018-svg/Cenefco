<?php

namespace App\Http\Requests\Asesores;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsesorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'       => ['required', 'string', 'max:200'],
            'telefono'     => ['required', 'string', 'max:30', 'unique:web_asesor,telefono'],
            'email'        => ['nullable', 'email', 'max:100'],
            'especialidad' => ['nullable', 'string', 'max:200'],
            'disponible'   => ['boolean'],
            'activo'       => ['boolean'],
        ];
    }
}
