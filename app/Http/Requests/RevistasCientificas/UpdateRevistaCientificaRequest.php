<?php

namespace App\Http\Requests\RevistasCientificas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRevistaCientificaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo_revistacientifica'      => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion_revistacientifica' => ['nullable', 'string'],
            'fecha_publicacion'             => ['nullable', 'date'],
            'archivo'                       => ['nullable', 'string', 'max:200'],
            'estado'                        => ['nullable', 'integer'],
        ];
    }
}
