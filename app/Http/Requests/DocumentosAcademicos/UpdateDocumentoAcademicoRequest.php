<?php

namespace App\Http\Requests\DocumentosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentoAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_fechapago'          => ['nullable', 'integer', 'min:1'],
            'id_fechadoc'           => ['nullable', 'integer', 'min:1'],
            'fecha_dejo_fisico'     => ['nullable', 'date'],
            'dejo_documento_fisico' => ['nullable', 'integer', 'in:0,1'],
            'documento_digital'     => ['nullable', 'string', 'max:255'],
            'observacion_doc'       => ['nullable', 'string', 'max:500'],
            'estado'                => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
