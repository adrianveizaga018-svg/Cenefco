<?php

namespace App\Http\Requests\Convenios;

use Illuminate\Foundation\Http\FormRequest;

class StoreConvenioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'            => ['required', 'string', 'max:300'],
            'institucion'       => ['nullable', 'string'],
            'tipo'              => ['nullable', 'string'],
            'descripcion'       => ['nullable', 'string'],
            'responsable'       => ['nullable', 'string'],
            'contacto_email'    => ['nullable', 'email'],
            'contacto_telefono' => ['nullable', 'string'],
            'fecha_inicio'      => ['nullable', 'date'],
            'fecha_fin'         => ['nullable', 'date'],
            'documento_url'     => ['nullable', 'string'],
            'logo_url'          => ['nullable', 'string'],
            'estado'            => ['nullable', 'string'],
            'orden'             => ['nullable', 'integer'],
        ];
    }
}
