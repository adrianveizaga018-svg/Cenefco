<?php

namespace App\Http\Requests\Redirecciones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRedireccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('redireccion') ?? $this->route('id');

        return [
            'url_origen'  => ['sometimes', 'required', 'string', 'max:500', "unique:web_redireccion,url_origen,{$id}"],
            'url_destino' => ['sometimes', 'required', 'string', 'max:500'],
            'codigo_http' => ['nullable', 'integer', 'in:301,302'],
            'activo'      => ['nullable', 'boolean'],
            'notas'       => ['nullable', 'string', 'max:300'],
        ];
    }
}
