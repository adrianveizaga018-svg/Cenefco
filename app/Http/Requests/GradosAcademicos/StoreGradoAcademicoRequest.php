<?php

namespace App\Http\Requests\GradosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradoAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'          => 'required|string|max:255',
            'abreviatura'     => 'required|string|max:20',
            'requiere_titulo' => 'nullable|boolean',
            'orden'           => 'nullable|integer',
            'activo'          => 'nullable|boolean',
        ];
    }
}
