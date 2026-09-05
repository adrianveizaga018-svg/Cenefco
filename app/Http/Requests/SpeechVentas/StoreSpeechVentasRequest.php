<?php

namespace App\Http\Requests\SpeechVentas;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpeechVentasRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo'         => 'required|string|max:200',
            'contenido'      => 'required|string',
            'categoria'      => 'nullable|string|max:80',
            'palabras_clave' => 'nullable|string|max:500',
            'activo'         => 'boolean',
            'orden'          => 'integer|min:0',
        ];
    }
}
