<?php

namespace App\Http\Requests\PreguntasFrecuentes;

use Illuminate\Foundation\Http\FormRequest;

class StorePreguntaFrecuenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pregunta' => ['required', 'string', 'max:500'],
            'respuesta' => ['required', 'string'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
