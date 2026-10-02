<?php

namespace App\Http\Requests\Revistas;

use Illuminate\Foundation\Http\FormRequest;

class StoreRevistaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_revista'          => ['required', 'integer'],
            'id_us_reg'           => ['nullable', 'integer'],
            'num_revista'         => ['nullable', 'integer'],
            'titulo_revista'      => ['required', 'string', 'max:200'],
            'descripcion_revista' => ['nullable', 'string'],
            'fecha_publicacion'   => ['nullable', 'date'],
            'archivo'             => ['nullable', 'string', 'max:200'],
            'estado'              => ['nullable', 'integer'],
        ];
    }
}
