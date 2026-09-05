<?php

namespace App\Http\Requests\Inscripciones;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_fechadoc'           => ['required', 'integer', 'min:1'],
            'dejo_documento_fisico' => ['nullable', 'boolean'],
            'documento_digital'     => ['nullable', 'string', 'max:500'],
            'observacion_doc'       => ['nullable', 'string'],
        ];
    }
}
