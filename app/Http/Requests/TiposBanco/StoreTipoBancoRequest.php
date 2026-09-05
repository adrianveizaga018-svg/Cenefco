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
            'activo' => ['nullable', 'boolean'],
            'orden'  => ['nullable', 'integer'],
        ];
    }
}
