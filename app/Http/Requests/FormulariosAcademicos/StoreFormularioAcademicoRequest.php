<?php

namespace App\Http\Requests\FormulariosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormularioAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_formulario' => 'required|integer',
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string|max:500',
            'tipo'          => 'nullable|string|max:100',
        ];
    }
}
