<?php

namespace App\Http\Requests\EnviosCertificado;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnvioCertificadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $departamentos = [
            'La Paz', 'Cochabamba', 'Santa Cruz', 'Oruro',
            'Potosí', 'Chuquisaca', 'Tarija', 'Beni', 'Pando',
        ];

        return [
            'id_ins'         => ['required', 'integer'],
            'departamento'   => ['nullable', 'string', 'in:' . implode(',', $departamentos)],
            'ciudad_destino' => ['required', 'string', 'max:150'],
            'fecha_envio'    => ['required', 'date'],
            'imagen_guia'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'aclaraciones'   => ['nullable', 'string'],
            'costo'          => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
