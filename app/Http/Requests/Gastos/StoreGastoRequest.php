<?php

namespace App\Http\Requests\Gastos;

use Illuminate\Foundation\Http\FormRequest;

class StoreGastoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'categoria_gasto_id' => ['required', 'integer', 'exists:categoria_gasto,id'],
            'concepto'           => ['required', 'string', 'max:200'],
            'monto'              => ['required', 'numeric', 'min:0.01'],
            'fecha'              => ['required', 'date'],
            'responsable'        => ['nullable', 'string', 'max:150'],
            'comprobante'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'nota'               => ['nullable', 'string'],
            'campana_publicidad_id' => ['nullable', 'integer', 'exists:campana_publicidad,id'],
        ];
    }
}
