<?php

namespace App\Http\Requests\Trivia;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreTriviaPreguntaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_id' => ['required', 'integer', 'exists:trivia_categorias,id'],
            'nivel_id' => ['required', 'integer', 'exists:trivia_niveles,id'],
            'enunciado' => ['required', 'string'],
            'imagen_url' => ['nullable', 'string', 'max:500'],
            'tiempo_limite_segundos' => ['nullable', 'integer', 'min:5', 'max:120'],
            'activo' => ['nullable', 'boolean'],
            'opciones' => ['required', 'array', 'min:2'],
            'opciones.*.texto' => ['required', 'string', 'max:300'],
            'opciones.*.es_correcta' => ['required', 'boolean'],
            'opciones.*.orden' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $opciones = $this->input('opciones', []);
            $correctas = collect($opciones)->where('es_correcta', true)->count();

            if ($correctas !== 1) {
                $validator->errors()->add('opciones', 'Debe marcarse exactamente una opción como correcta.');
            }
        });
    }
}
