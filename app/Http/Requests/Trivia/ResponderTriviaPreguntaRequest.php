<?php

namespace App\Http\Requests\Trivia;

use Illuminate\Foundation\Http\FormRequest;

class ResponderTriviaPreguntaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pregunta_id' => ['required', 'integer', 'exists:trivia_preguntas,id'],
            'opcion_id' => ['nullable', 'integer', 'exists:trivia_opciones,id'],
            'tiempo_respuesta_ms' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
