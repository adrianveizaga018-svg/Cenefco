<?php

namespace App\Http\Requests\SugerenciasReclamos;

use Illuminate\Foundation\Http\FormRequest;

class StoreSugerenciaReclamoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asunto' => ['required', 'string', 'max:300'],
            'mensaje' => ['nullable', 'string'],
            'email_respuesta' => ['nullable', 'email', 'max:150'],
            'secretaria_destino_id' => ['nullable', 'integer', 'exists:secretarias,id'],
        ];
    }
}
