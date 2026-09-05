<?php

namespace App\Http\Requests\UsuariosPrograma;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioProgramaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_programa'     => ['nullable', 'integer'],
            'id_tipoprograma' => ['nullable', 'integer'],
            'estado'          => ['nullable', 'integer'],
        ];
    }
}
