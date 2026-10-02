<?php

namespace App\Http\Requests\TiposBanco;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoBancoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150', 'unique:tipos_banco,nombre'],
            'numero_cuenta' => ['nullable', 'string', 'max:100'],
            'titular' => ['nullable', 'string', 'max:150'],
            'activo' => ['nullable', 'boolean'],
            'orden'  => ['nullable', 'integer'],
        ];
    }
}
