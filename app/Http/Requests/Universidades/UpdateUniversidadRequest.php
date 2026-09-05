<?php

namespace App\Http\Requests\Universidades;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUniversidadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_universidad' => 'sometimes|string|max:255',
            'id_ciudad'          => 'sometimes|integer',
            'id_tipouniversidad' => 'sometimes|integer',
        ];
    }
}
