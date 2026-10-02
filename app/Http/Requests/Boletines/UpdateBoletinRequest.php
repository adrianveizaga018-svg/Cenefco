<?php

namespace App\Http\Requests\Boletines;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoletinRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo_pagina'       => ['nullable', 'string', 'max:200'],
            'titulo_boletin'      => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion_boletin' => ['nullable', 'string'],
            'estado'              => ['nullable', 'integer'],
            'imagen_url'          => ['nullable', 'string', 'max:255'],
        ];
    }
}
