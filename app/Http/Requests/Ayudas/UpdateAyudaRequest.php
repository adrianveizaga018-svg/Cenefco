<?php

namespace App\Http\Requests\Ayudas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAyudaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'num_ayuda'             => 'sometimes|string|max:50',
            'id_us'                 => 'sometimes|integer',
            'gestion'               => 'sometimes|string|max:10',
            'monto_pagado'          => 'sometimes|nullable|numeric',
            'nro_recibo'            => 'sometimes|nullable|string|max:50',
            'fecha_recibo'          => 'sometimes|nullable|date',
            'observacion_pago'      => 'sometimes|nullable|string',
            'id_categoriatipoayuda' => 'sometimes|nullable|integer',
        ];
    }
}
