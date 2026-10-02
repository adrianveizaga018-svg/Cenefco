<?php

namespace App\Http\Requests\TiposPostgrado;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoPostgradoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tipopost'        => ['required', 'integer'],
            'id_plan'            => ['required', 'integer'],
            'id_us_reg'          => ['nullable', 'integer'],
            'num_tipopost'       => ['nullable', 'integer'],
            'id_tipopago'        => ['nullable', 'integer'],
            'descuentopostgrado' => ['nullable', 'string', 'max:200'],
            'calculo_cuota'      => ['nullable', 'string', 'max:200'],
            'estado'             => ['nullable', 'integer'],
        ];
    }
}
