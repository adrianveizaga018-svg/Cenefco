<?php

namespace App\Http\Requests\TiposPrograma;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoProgramaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tipoprograma'     => ['required', 'integer'],
            'nombre_tipoprograma' => ['required', 'string', 'max:200'],
            'estado'              => ['nullable', 'integer'],
        ];
    }
}
