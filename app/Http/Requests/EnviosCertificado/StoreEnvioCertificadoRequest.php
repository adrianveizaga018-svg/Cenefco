<?php

namespace App\Http\Requests\EnviosCertificado;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnvioCertificadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_ins'         => ['required', 'integer'],
            'ciudad_destino' => ['required', 'string', 'max:150'],
            'fecha_envio'    => ['required', 'date'],
            'imagen_guia'    => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'aclaraciones'   => ['nullable', 'string'],
            'costo'          => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
