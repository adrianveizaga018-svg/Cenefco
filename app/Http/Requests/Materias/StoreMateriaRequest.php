<?php

namespace App\Http\Requests\Materias;

use Illuminate\Foundation\Http\FormRequest;

class StoreMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_mat'        => ['required', 'integer', 'unique:t_materia,id_mat'],
            'id_us_reg'     => ['nullable', 'integer'],
            'sigla'         => ['nullable', 'string', 'max:200'],
            'nombremat'     => ['nullable', 'string', 'max:200'],
            'nombre'        => ['required', 'string', 'max:200'],
            'semestre'      => ['nullable', 'string', 'max:200'],
            'modalidad'     => ['nullable', 'integer'],
            'carga_horaria' => ['nullable', 'string', 'max:200'],
            'observacion'   => ['nullable', 'string'],
            'estado'        => ['nullable', 'integer'],
        ];
    }
}
