<?php

namespace App\Http\Requests\CertificadosModelo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificadoModeloRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'url_imagen'  => 'nullable|string|max:500',
            'contenido'   => 'nullable|string',
        ];
    }
}
