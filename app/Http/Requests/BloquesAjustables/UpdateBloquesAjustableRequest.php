<?php

namespace App\Http\Requests\BloquesAjustables;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBloquesAjustableRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_pagina'          => 'nullable|integer',
            'id_bloqueplantilla' => 'nullable|integer',
            'nombre'             => 'nullable|string|max:255',
            'orden'              => 'nullable|integer',
        ];
    }
}
