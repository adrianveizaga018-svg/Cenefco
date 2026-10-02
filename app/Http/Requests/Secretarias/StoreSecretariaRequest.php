<?php

namespace App\Http\Requests\Secretarias;

use Illuminate\Foundation\Http\FormRequest;

class StoreSecretariaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:200'],
            'sigla' => ['nullable', 'string', 'max:200'],
            'atribuciones' => ['nullable', 'string'],
            'direccion_fisica' => ['nullable', 'string', 'max:200'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'horario_atencion' => ['nullable', 'string', 'max:50'],
            'foto_titular_url' => ['nullable', 'string', 'max:255'],
            'orden_organigrama' => ['nullable', 'integer'],
            'activa' => ['nullable', 'boolean'],
        ];
    }
}
