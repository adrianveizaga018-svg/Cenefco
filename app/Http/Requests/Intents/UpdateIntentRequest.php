<?php

namespace App\Http\Requests\Intents;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('intent') ?? $this->route('id');

        return [
            'nombre'              => 'required|string|max:120',
            'slug'                => "required|string|max:60|unique:web_intents,slug,{$id}|regex:/^[a-z0-9_]+$/",
            'dominio'             => 'required|string|in:general,academico,contenido',
            'prioridad'           => 'required|integer|min:100|max:1000',
            'accion'              => 'required|string|max:60',
            'activo'              => 'boolean',
            'orden'               => 'integer|min:0',
            'eventos'             => 'nullable|array',
            'input_contexts'      => 'nullable|array',
            'output_contexts'     => 'nullable|array',
            'frases_entrenamiento'=> 'nullable|array',
            'respuestas'          => 'nullable|array',
        ];
    }
}
