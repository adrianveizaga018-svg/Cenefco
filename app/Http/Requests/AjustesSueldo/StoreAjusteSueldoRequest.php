<?php

namespace App\Http\Requests\AjustesSueldo;

use Illuminate\Foundation\Http\FormRequest;

class StoreAjusteSueldoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'empleado_id' => ['required', 'integer', 'exists:empleado,id'],
            'anio'        => ['required', 'integer', 'min:2020', 'max:2100'],
            'mes'         => ['required', 'integer', 'min:1', 'max:12'],
            'tipo'        => ['required', 'string', 'in:descuento,bono'],
            'monto'       => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'motivo'      => ['required', 'string', 'max:300'],
        ];
    }
}
