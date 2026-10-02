<?php

namespace App\Http\Requests\FechasDoc;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFechaDocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nro_doc'        => ['nullable', 'string', 'max:200'],
            'tipo_documento' => ['nullable', 'string', 'max:200'],
            'fecha_inicio'   => ['nullable', 'date'],
            'fecha_fin'      => ['nullable', 'date'],
            'obligatorio'    => ['nullable', 'integer'],
            'estado'         => ['nullable', 'integer'],
        ];
    }
}
