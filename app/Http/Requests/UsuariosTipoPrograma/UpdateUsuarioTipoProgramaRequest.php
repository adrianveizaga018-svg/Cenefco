<?php

namespace App\Http\Requests\UsuariosTipoPrograma;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioTipoProgramaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_tipoprograma' => ['nullable', 'integer'],
            'estado'          => ['nullable', 'integer'],
        ];
    }
}
