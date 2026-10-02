<?php

namespace App\Http\Requests\Honorarios;

use Illuminate\Foundation\Http\FormRequest;

class UpsertConfigHonorarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_programa'    => ['required', 'integer'],
            'tipo_honorario' => ['required', 'string', 'in:diplomado_fijo,rm_por_dia,aval_por_dia'],
            'monto_fijo'     => ['required_if:tipo_honorario,diplomado_fijo', 'nullable', 'numeric', 'min:0.01'],
            'monto_por_dia'  => ['required_if:tipo_honorario,rm_por_dia,aval_por_dia', 'nullable', 'numeric', 'min:0.01'],
        ];
    }
}
