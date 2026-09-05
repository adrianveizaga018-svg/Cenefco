<?php

namespace App\Http\Requests\Fotos;

use Illuminate\Foundation\Http\FormRequest;

class StoreFotoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_foto'          => ['required', 'integer'],
            'id_us_reg'        => ['nullable', 'integer'],
            'num_foto'         => ['nullable', 'integer'],
            'titulo_foto'      => ['required', 'string', 'max:200'],
            'descripcion_foto' => ['nullable', 'string'],
            'foto'             => ['nullable', 'string', 'max:200'],
            'fecha_foto'       => ['nullable', 'date'],
            'estado'           => ['nullable', 'integer'],
        ];
    }
}
