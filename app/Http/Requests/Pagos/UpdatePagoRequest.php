<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'monto_pagado'        => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'nro_boleta_bancaria' => ['nullable', 'string', 'max:200'],
            'tipo_banco_id'       => ['nullable', 'integer', 'exists:tipos_banco,id'],
            'fecha_deposito'      => ['nullable', 'date'],
            'observacion_pago'    => ['nullable', 'string'],
            'comprobante_archivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'monto_descuento'     => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'motivo_descuento'    => [
                Rule::requiredIf(fn () => (float) $this->input('monto_descuento', 0) > 0),
                'nullable', 'string', 'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_descuento.required' => 'Indica el motivo del descuento.',
        ];
    }
}
