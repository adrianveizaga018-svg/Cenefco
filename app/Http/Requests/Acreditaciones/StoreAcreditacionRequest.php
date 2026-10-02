<?php

namespace App\Http\Requests\Acreditaciones;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcreditacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'            => ['required', 'string', 'max:200'],
            'entidad_otorgante' => ['required', 'string', 'max:200'],
            'tipo'              => ['nullable', 'string', 'max:100'],
            'descripcion'       => ['nullable', 'string'],
            'logo_url'          => ['nullable', 'string', 'max:255'],
            'logo_alt'          => ['nullable', 'string', 'max:255'],
            'url_verificacion'  => ['nullable', 'string', 'max:255'],
            'fecha_obtencion'   => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_obtencion'],
            'orden'             => ['nullable', 'integer', 'min:0'],
            'activo'            => ['nullable', 'boolean'],
        ];
    }
}
