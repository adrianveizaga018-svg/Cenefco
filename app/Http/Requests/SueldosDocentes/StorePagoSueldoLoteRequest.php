<?php

namespace App\Http\Requests\SueldosDocentes;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoSueldoLoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cuotas'                   => ['required', 'array', 'min:1', 'max:60'],
            'cuotas.*.monto_pagado'    => ['required', 'numeric', 'min:0.01'],
            'cuotas.*.fecha_pago'      => ['required', 'date'],
            'cuotas.*.nro_comprobante' => ['nullable', 'string', 'max:100'],
            'cuotas.*.observacion'     => ['nullable', 'string'],
        ];
    }
}
