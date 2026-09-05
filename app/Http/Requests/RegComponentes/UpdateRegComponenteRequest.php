<?php

namespace App\Http\Requests\RegComponentes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegComponenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'tipo'        => 'nullable|string|max:100',
            'icono'       => 'nullable|string|max:255',
        ];
    }
}
