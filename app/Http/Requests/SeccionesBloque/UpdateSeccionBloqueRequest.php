<?php

namespace App\Http\Requests\SeccionesBloque;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeccionBloqueRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_bloqueajustable' => 'nullable|integer',
            'nombre'             => 'nullable|string|max:255',
            'contenido'          => 'nullable|string',
            'orden'              => 'nullable|integer',
        ];
    }
}
