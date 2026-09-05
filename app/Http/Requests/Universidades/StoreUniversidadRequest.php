<?php

namespace App\Http\Requests\Universidades;

use Illuminate\Foundation\Http\FormRequest;

class StoreUniversidadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_universidad'  => 'required|string|max:255',
            'id_ciudad'           => 'required|integer',
            'id_tipouniversidad'  => 'required|integer',
            'id_us_reg'           => 'required|integer',
        ];
    }
}
