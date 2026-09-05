<?php

namespace App\Http\Requests\FechasDoc;

use Illuminate\Foundation\Http\FormRequest;

class StoreFechaDocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_fechadoc'    => ['required', 'integer', 'unique:t_fechadoc,id_fechadoc'],
            'id_plandoc'     => ['required', 'integer'],
            'id_us_reg'      => ['nullable', 'integer'],
            'num_fechadoc'   => ['nullable', 'integer'],
            'nro_doc'        => ['nullable', 'string', 'max:200'],
            'tipo_documento' => ['nullable', 'string', 'max:200'],
            'fecha_inicio'   => ['nullable', 'date'],
            'fecha_fin'      => ['nullable', 'date'],
            'obligatorio'    => ['nullable', 'integer'],
            'estado'         => ['nullable', 'integer'],
        ];
    }
}
