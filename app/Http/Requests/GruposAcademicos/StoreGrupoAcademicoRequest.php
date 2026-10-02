<?php

namespace App\Http\Requests\GruposAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrupoAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_grupo'    => 'required|integer',
            'id_test'     => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'orden'       => 'nullable|integer',
        ];
    }
}
