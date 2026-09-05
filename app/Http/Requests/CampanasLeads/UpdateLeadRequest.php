<?php

namespace App\Http\Requests\CampanasLeads;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => ['sometimes', 'required', 'string', 'max:150'],
            'celular'   => ['sometimes', 'required', 'string', 'max:30'],
            'correo'    => ['nullable', 'email', 'max:150'],
            'profesion' => ['nullable', 'string', 'max:150'],
        ];
    }
}
