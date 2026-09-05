<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $metodo = $this->input('metodo_pago');

        return [
            'id_us'               => ['required', 'integer'],
            'id_fechapago'        => ['nullable', 'integer'],
            'monto_pagado'        => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'nro_boleta_bancaria' => $metodo === 'deposito_bancario'
                                       ? ['required', 'string', 'max:200']
                                       : ['nullable', 'string', 'max:200'],
            'fecha_deposito'      => ['nullable', 'date'],
            'nro_nit'             => ['nullable', 'string', 'max:200'],
            'nombre_nit'          => ['nullable', 'string', 'max:200'],
            'tipo_fechapago'      => ['nullable', 'integer'],
            'observacion_pago'    => ['nullable', 'string'],
            'metodo_pago'         => ['nullable', 'string', 'in:efectivo,deposito_bancario,pago_online,qr'],
            'id_ins'              => ['nullable', 'integer'],
            'comprobante_archivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'tipo_banco_id'       => ['nullable', 'integer', 'exists:tipos_banco,id'],
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
            'nro_boleta_bancaria.required' => 'El número de boleta es obligatorio para depósito bancario.',
            'motivo_descuento.required'    => 'Indica el motivo del descuento.',
        ];
    }
}
