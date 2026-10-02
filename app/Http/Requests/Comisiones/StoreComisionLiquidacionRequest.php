<?php

namespace App\Http\Requests\Comisiones;

use Illuminate\Foundation\Http\FormRequest;

class StoreComisionLiquidacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'vendedor_id' => ['required', 'integer', 'exists:vendedores,id'],
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
            'nota'        => ['nullable', 'string', 'max:1000'],
        ];
    }
}
