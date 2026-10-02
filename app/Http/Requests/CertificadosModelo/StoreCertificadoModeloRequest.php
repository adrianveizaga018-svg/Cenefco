<?php

namespace App\Http\Requests\CertificadosModelo;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificadoModeloRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_certmod'  => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'url_imagen'  => 'nullable|string|max:500',
            'contenido'   => 'nullable|string',
        ];
    }
}
