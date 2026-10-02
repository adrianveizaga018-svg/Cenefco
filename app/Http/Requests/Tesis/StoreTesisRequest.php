<?php

namespace App\Http\Requests\Tesis;

use Illuminate\Foundation\Http\FormRequest;

class StoreTesisRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tesis'          => ['required', 'integer'],
            'id_us_reg'         => ['nullable', 'integer'],
            'num_tesis'         => ['nullable', 'integer'],
            'titulo_tesis'      => ['required', 'string', 'max:200'],
            'descripcion_tesis' => ['nullable', 'string'],
            'fecha_publicacion' => ['nullable', 'date'],
            'autor'             => ['nullable', 'string', 'max:200'],
            'tipo_tesis'        => ['nullable', 'integer'],
            'archivo'           => ['nullable', 'string', 'max:200'],
            'estado'            => ['nullable', 'integer'],
        ];
    }
}
