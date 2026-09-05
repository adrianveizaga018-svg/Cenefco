<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFechaPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'monto_a_pagar' => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'fecha_inicio'  => ['nullable', 'date'],
            'fecha_fin'     => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'obligatorio'   => ['nullable', 'boolean'],
            'tipo_tramite'  => ['nullable', 'string', 'max:100'],
            'estado'        => ['nullable', 'integer'],
        ];
    }
}
