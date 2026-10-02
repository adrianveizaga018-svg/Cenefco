<?php

namespace App\Http\Requests\TestsAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'        => 'nullable|string|max:255',
            'descripcion'   => 'nullable|string|max:500',
            'habilitar_test'=> 'nullable|boolean',
        ];
    }
}
