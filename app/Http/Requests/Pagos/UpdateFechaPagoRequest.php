<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFechaPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_plan'       => ['nullable', 'integer'],
            'nro_pago'      => ['nullable', 'string', 'max:20'],
            'monto_a_pagar' => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'dias_desde_inscripcion' => ['nullable', 'integer', 'min:0'],
            'fecha_inicio'  => ['nullable', 'date'],
            'fecha_fin'     => ['nullable', 'date'],
            'obligatorio'   => ['nullable', 'boolean'],
            'tipo_tramite'  => ['nullable', 'string', 'max:100'],
            'estado'        => ['nullable', 'integer'],
        ];
    }
}
