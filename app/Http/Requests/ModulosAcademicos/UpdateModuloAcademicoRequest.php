<?php

namespace App\Http\Requests\ModulosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuloAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:100',
            'posicion'    => 'nullable|integer',
            'url'         => 'nullable|string|max:500',
        ];
    }
}
