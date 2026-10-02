<?php

namespace App\Http\Requests\SugerenciasReclamos;

use Illuminate\Foundation\Http\FormRequest;

class RespondSugerenciaReclamoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'respuesta' => ['required', 'string'],
            'estado' => ['sometimes', 'required', 'string', 'in:en_proceso,resuelto'],
        ];
    }
}
