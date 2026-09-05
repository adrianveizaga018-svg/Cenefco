<?php

namespace App\Http\Requests\Trivia;

use Illuminate\Foundation\Http\FormRequest;

class StoreTriviaNivelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_id' => ['required', 'integer', 'exists:trivia_categorias,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'puntaje_base' => ['nullable', 'integer', 'min:1'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
