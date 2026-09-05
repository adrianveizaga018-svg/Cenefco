<?php

namespace App\Http\Requests\TiposPostgrado;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoPostgradoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tipopago'        => ['nullable', 'integer'],
            'descuentopostgrado' => ['nullable', 'string', 'max:200'],
            'calculo_cuota'      => ['nullable', 'string', 'max:200'],
            'estado'             => ['nullable', 'integer'],
        ];
    }
}
