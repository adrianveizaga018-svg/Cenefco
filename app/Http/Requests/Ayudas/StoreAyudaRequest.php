<?php

namespace App\Http\Requests\Ayudas;

use Illuminate\Foundation\Http\FormRequest;

class StoreAyudaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_ayuda'              => 'required|integer',
            'id_us_reg'             => 'required|integer',
            'num_ayuda'             => 'required|string|max:50',
            'id_us'                 => 'required|integer',
            'gestion'               => 'required|string|max:10',
            'monto_pagado'          => 'nullable|numeric',
            'nro_recibo'            => 'nullable|string|max:50',
            'fecha_recibo'          => 'nullable|date',
            'observacion_pago'      => 'nullable|string',
            'id_categoriatipoayuda' => 'nullable|integer',
        ];
    }
}
