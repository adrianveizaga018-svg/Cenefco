<?php

namespace App\Http\Requests\MenusAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_mod'      => 'nullable|integer',
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'url'         => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:100',
            'orden'       => 'nullable|integer',
        ];
    }
}
