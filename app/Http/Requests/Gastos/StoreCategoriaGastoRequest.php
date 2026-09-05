<?php

namespace App\Http\Requests\Gastos;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaGastoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'        => ['required', 'string', 'max:150', 'unique:categoria_gasto,nombre'],
            'linea_negocio' => ['nullable', 'string', 'max:50'],
            'activo'        => ['nullable', 'boolean'],
        ];
    }
}
