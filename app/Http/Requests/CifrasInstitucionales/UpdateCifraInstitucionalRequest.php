<?php

namespace App\Http\Requests\CifrasInstitucionales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCifraInstitucionalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'valor'       => ['sometimes', 'required', 'string', 'max:50'],
            'etiqueta'    => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:300'],
            'icono'       => ['nullable', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'max:7'],
            'orden'       => ['nullable', 'integer'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }
}
