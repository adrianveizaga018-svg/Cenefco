<?php

namespace App\Http\Requests\Monografias;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonografiaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_monografia'          => ['required', 'integer'],
            'id_us_reg'              => ['nullable', 'integer'],
            'num_monografia'         => ['nullable', 'integer'],
            'titulo_monografia'      => ['required', 'string', 'max:200'],
            'descripcion_monografia' => ['nullable', 'string'],
            'fecha_publicacion'      => ['nullable', 'date'],
            'autor'                  => ['nullable', 'string', 'max:200'],
            'archivo'                => ['nullable', 'string', 'max:200'],
            'estado'                 => ['nullable', 'integer'],
        ];
    }
}
