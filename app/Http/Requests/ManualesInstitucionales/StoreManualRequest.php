<?php

namespace App\Http\Requests\ManualesInstitucionales;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'string', 'max:50'],
            'titulo' => ['required', 'string', 'max:300'],
            'descripcion' => ['nullable', 'string'],
            'archivo_url' => ['nullable', 'string', 'max:255'],
            'formato' => ['nullable', 'string', 'max:10'],
            'numero_paginas' => ['nullable', 'integer'],
            'gestion' => ['required', 'integer'],
            'version' => ['nullable', 'integer'],
            'vigente' => ['nullable', 'boolean'],
            'fecha_publicacion' => ['nullable', 'date'],
        ];
    }
}
