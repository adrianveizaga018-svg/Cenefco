<?php

namespace App\Http\Requests\Reportes;

use Illuminate\Foundation\Http\FormRequest;

class CuotasCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_imp'        => ['nullable', 'integer'],
            'periodo'       => ['nullable', 'in:dia,mes,anio,rango'],
            'fecha'         => ['nullable', 'date'],
            'fecha_inicio'  => ['nullable', 'date'],
            'fecha_fin'     => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'con_inactivos' => ['nullable', 'boolean'],
        ];
    }
}
