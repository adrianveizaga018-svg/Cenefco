<?php

namespace App\Http\Requests\BloquesAjustables;

use Illuminate\Foundation\Http\FormRequest;

class StoreBloquesAjustableRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_bloqueajustable'    => 'required|integer',
            'id_pagina'             => 'required|integer',
            'id_bloqueplantilla'    => 'required|integer',
            'nombre'                => 'nullable|string|max:255',
            'orden'                 => 'nullable|integer',
        ];
    }
}
