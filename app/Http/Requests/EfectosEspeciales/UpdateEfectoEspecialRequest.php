<?php

namespace App\Http\Requests\EfectosEspeciales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEfectoEspecialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'           => ['sometimes', 'required', 'string', 'max:100'],
            'tipo_efecto'      => ['sometimes', 'required', 'string', 'in:nieve,confetti,hojas,estrellas'],
            'color_primario'   => ['nullable', 'string', 'max:7'],
            'color_secundario' => ['nullable', 'string', 'max:7'],
            'fecha_inicio'     => ['sometimes', 'required', 'date'],
            'fecha_fin'        => ['sometimes', 'required', 'date'],
            'intensidad'       => ['nullable', 'integer', 'min:0', 'max:100'],
            'activo'           => ['nullable', 'boolean'],
        ];
    }
}
