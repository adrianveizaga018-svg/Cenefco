<?php

namespace App\Http\Requests\SeccionesBloque;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeccionBloqueRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_seccionbloque'   => 'required|integer',
            'id_bloqueajustable' => 'required|integer',
            'nombre'             => 'required|string|max:255',
            'contenido'          => 'nullable|string',
            'orden'              => 'nullable|integer',
        ];
    }
}
