<?php

namespace App\Http\Requests\Aliados;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAliadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => ['sometimes', 'required', 'string', 'max:200'],
            'logo_url'    => ['sometimes', 'required', 'string', 'max:255'],
            'logo_alt'    => ['nullable', 'string', 'max:255'],
            'url_sitio'   => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'tipo'        => ['nullable', 'string', 'max:100'],
            'orden'       => ['nullable', 'integer'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }
}
