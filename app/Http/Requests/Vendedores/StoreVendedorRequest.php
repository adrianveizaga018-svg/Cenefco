<?php

namespace App\Http\Requests\Vendedores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendedorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:150'],
            'apellido'    => ['required', 'string', 'max:150'],
            'ci'          => ['nullable', 'string', 'max:20', Rule::unique('vendedores', 'ci')->whereNull('deleted_at')],
            'telefono'    => ['nullable', 'string', 'max:30'],
            'email'       => ['nullable', 'email', 'max:255', Rule::unique('vendedores', 'email')->whereNull('deleted_at')],
            'foto'        => ['nullable', 'string', 'max:500'],
            'pagina'      => ['nullable', 'string', 'max:150'],
            'meta_ventas' => ['nullable', 'numeric', 'min:0'],
            'activo'      => ['nullable', 'boolean'],
            'usuario_id'  => ['nullable', 'integer', 'exists:usuarios,id'],
        ];
    }
}
