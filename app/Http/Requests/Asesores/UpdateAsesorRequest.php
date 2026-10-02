<?php

namespace App\Http\Requests\Asesores;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAsesorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('asesor') ?? $this->route('id');

        return [
            'nombre'       => ['sometimes', 'string', 'max:200'],
            'telefono'     => ['sometimes', 'string', 'max:30', "unique:web_asesor,telefono,{$id}"],
            'email'        => ['nullable', 'email', 'max:100'],
            'especialidad' => ['nullable', 'string', 'max:200'],
            'disponible'   => ['boolean'],
            'activo'       => ['boolean'],
        ];
    }
}
