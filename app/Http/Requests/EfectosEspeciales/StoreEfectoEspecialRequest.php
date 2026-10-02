<?php

namespace App\Http\Requests\EfectosEspeciales;

use Illuminate\Foundation\Http\FormRequest;

class StoreEfectoEspecialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'           => ['required', 'string', 'max:100'],
            'tipo_efecto'      => ['required', 'string', 'in:nieve,confetti,hojas,estrellas'],
            'color_primario'   => ['nullable', 'string', 'max:7'],
            'color_secundario' => ['nullable', 'string', 'max:7'],
            'fecha_inicio'     => ['required', 'date'],
            'fecha_fin'        => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'intensidad'       => ['nullable', 'integer', 'min:0', 'max:100'],
            'activo'           => ['nullable', 'boolean'],
        ];
    }
}
