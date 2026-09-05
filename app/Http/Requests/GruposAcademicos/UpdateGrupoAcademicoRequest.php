<?php

namespace App\Http\Requests\GruposAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrupoAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_test'     => 'nullable|integer',
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'orden'       => 'nullable|integer',
        ];
    }
}
