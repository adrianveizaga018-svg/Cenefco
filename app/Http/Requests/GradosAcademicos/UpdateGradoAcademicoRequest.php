<?php

namespace App\Http\Requests\GradosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradoAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'          => 'sometimes|string|max:255',
            'abreviatura'     => 'sometimes|string|max:20',
            'requiere_titulo' => 'sometimes|boolean',
            'orden'           => 'sometimes|nullable|integer',
            'activo'          => 'sometimes|boolean',
        ];
    }
}
