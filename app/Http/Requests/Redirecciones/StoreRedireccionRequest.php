<?php

namespace App\Http\Requests\Redirecciones;

use Illuminate\Foundation\Http\FormRequest;

class StoreRedireccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url_origen'  => ['required', 'string', 'max:500', 'unique:web_redireccion,url_origen'],
            'url_destino' => ['required', 'string', 'max:500'],
            'codigo_http' => ['nullable', 'integer', 'in:301,302'],
            'activo'      => ['nullable', 'boolean'],
            'notas'       => ['nullable', 'string', 'max:300'],
        ];
    }
}
