<?php

namespace App\Http\Requests\Gastos;

use Illuminate\Foundation\Http\FormRequest;

class StoreGastoRecurrenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'categoria_gasto_id' => ['required', 'integer', 'exists:categoria_gasto,id'],
            'concepto'           => ['required', 'string', 'max:200'],
            'monto'              => ['required', 'numeric', 'min:0.01'],
            'dia_del_mes'        => ['required', 'integer', 'min:1', 'max:31'],
            'activo'             => ['nullable', 'boolean'],
        ];
    }
}
