<?php

namespace App\Http\Requests\TestsAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_test'       => 'required|integer',
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string|max:500',
            'habilitar_test'=> 'nullable|boolean',
        ];
    }
}
