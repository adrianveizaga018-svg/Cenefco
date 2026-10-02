<?php

namespace App\Http\Requests\Inscripciones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_ins'       => ['nullable', 'date'],
            'id_plan'         => ['nullable', 'integer', 'min:1'],
            'observacion_ins' => ['nullable', 'string'],
            'observacion'     => ['nullable', 'string'],
            'estado'          => ['nullable', 'integer', 'in:0,1'],
            'documentos'      => ['nullable', 'array'],
            'documentos.*'    => ['nullable', 'string', 'max:500'],
            'campos_extra'    => ['nullable', 'array'],
        ];
    }
}
