<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class StoreFechaPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_plan'       => ['required', 'integer'],
            'nro_pago'      => ['nullable', 'string', 'max:20'],
            'monto_a_pagar' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'fecha_inicio'  => ['nullable', 'date'],
            'fecha_fin'     => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'obligatorio'   => ['nullable', 'boolean'],
            'tipo_tramite'  => ['nullable', 'string', 'max:100'],
        ];
    }
}
