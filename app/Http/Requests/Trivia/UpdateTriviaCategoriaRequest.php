<?php

namespace App\Http\Requests\Trivia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTriviaCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'imagen_url' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:20'],
            'curso_id' => ['nullable', 'integer'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
