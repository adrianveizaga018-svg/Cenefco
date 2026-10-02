<?php

namespace App\Http\Requests\TiposPrograma;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoProgramaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_tipoprograma' => ['sometimes', 'required', 'string', 'max:200'],
            'estado'              => ['nullable', 'integer'],
        ];
    }
}
