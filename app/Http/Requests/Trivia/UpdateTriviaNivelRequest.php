<?php

namespace App\Http\Requests\Trivia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTriviaNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_id' => ['sometimes', 'required', 'integer', 'exists:trivia_categorias,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'puntaje_base' => ['nullable', 'integer', 'min:1'],
            'activo' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
