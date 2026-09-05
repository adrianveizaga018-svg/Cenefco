<?php

namespace App\Http\Requests\SueldosDocentes;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoSueldoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monto_pagado'        => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'          => ['required', 'date'],
            'nro_comprobante'     => ['nullable', 'string', 'max:100'],
            'observacion'         => ['nullable', 'string'],
            'comprobante_archivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }
}
