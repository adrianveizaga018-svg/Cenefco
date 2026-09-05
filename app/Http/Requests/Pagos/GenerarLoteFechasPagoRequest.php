<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class GenerarLoteFechasPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_plan'      => ['required', 'integer'],
            'nro_cuotas'   => ['required', 'integer', 'min:1', 'max:60'],
            'monto_total'  => ['required', 'numeric', 'min:0.01'],
            'fecha_inicio' => ['required', 'date'],
            'intervalo'    => ['required', 'string', 'in:mensual,quincenal,semanal'],
            'tipo_tramite' => ['required', 'string', 'max:100'],
        ];
    }
}
