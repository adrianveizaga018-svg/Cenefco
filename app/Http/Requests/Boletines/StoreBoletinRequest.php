<?php

namespace App\Http\Requests\Boletines;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoletinRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_boletin'          => ['required', 'integer'],
            'id_us_reg'           => ['nullable', 'integer'],
            'num_boletin'         => ['nullable', 'integer'],
            'titulo_pagina'       => ['nullable', 'string', 'max:200'],
            'titulo_boletin'      => ['required', 'string', 'max:200'],
            'descripcion_boletin' => ['nullable', 'string'],
            'estado'              => ['nullable', 'integer'],
            'imagen_url'          => ['nullable', 'string', 'max:255'],
        ];
    }
}
