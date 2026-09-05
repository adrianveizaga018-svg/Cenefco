<?php

namespace App\Http\Requests\Materias;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sigla'         => ['nullable', 'string', 'max:200'],
            'nombremat'     => ['nullable', 'string', 'max:200'],
            'nombre'        => ['sometimes', 'required', 'string', 'max:200'],
            'semestre'      => ['nullable', 'string', 'max:200'],
            'modalidad'     => ['nullable', 'integer'],
            'carga_horaria' => ['nullable', 'string', 'max:200'],
            'observacion'   => ['nullable', 'string'],
            'estado'        => ['nullable', 'integer'],
        ];
    }
}
