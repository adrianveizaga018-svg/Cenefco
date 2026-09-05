<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnticipoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'monto_pagado'        => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'nro_boleta_bancaria' => ['nullable', 'string', 'max:200'],
            'fecha_deposito'      => ['nullable', 'date'],
            'observacion_pago'    => ['nullable', 'string'],
        ];
    }
}
