<?php

namespace App\Http\Requests\RevistasCientificas;

use Illuminate\Foundation\Http\FormRequest;

class StoreRevistaCientificaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_revistacientifica'          => ['required', 'integer'],
            'id_us_reg'                     => ['nullable', 'integer'],
            'num_revistacientifica'         => ['nullable', 'integer'],
            'titulo_revistacientifica'      => ['required', 'string', 'max:200'],
            'descripcion_revistacientifica' => ['nullable', 'string'],
            'fecha_publicacion'             => ['nullable', 'date'],
            'archivo'                       => ['nullable', 'string', 'max:200'],
            'estado'                        => ['nullable', 'integer'],
        ];
    }
}
