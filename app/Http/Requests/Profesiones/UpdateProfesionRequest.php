<?php

namespace App\Http\Requests\Profesiones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfesionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:255',
            'orden'  => 'sometimes|nullable|integer',
            'activo' => 'sometimes|boolean',
        ];
    }
}
