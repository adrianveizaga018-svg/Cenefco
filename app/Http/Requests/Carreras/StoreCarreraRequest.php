<?php

namespace App\Http\Requests\Carreras;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_carrera'    => ['required', 'integer', 'unique:t_carrera,id_carrera'],
            'id_us_reg'     => ['nullable', 'integer'],
            'num_carrera'   => ['nullable', 'integer'],
            'nombre_carrera' => ['required', 'string', 'max:200'],
            'estado'        => ['nullable', 'integer'],
        ];
    }
}
