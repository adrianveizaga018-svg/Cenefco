<?php

namespace App\Http\Requests\MensajesContacto;

use Illuminate\Foundation\Http\FormRequest;

class RespondMensajeContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'respuesta' => ['required', 'string'],
            'estado' => ['nullable', 'string', 'in:respondido,archivado'],
        ];
    }
}
