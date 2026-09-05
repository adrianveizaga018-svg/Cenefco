<?php

namespace App\Http\Requests\UsuariosPrograma;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioProgramaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_usuarioprograma'  => ['required', 'integer'],
            'id_us'               => ['required', 'integer'],
            'id_us_reg'           => ['nullable', 'integer'],
            'num_usuarioprograma' => ['nullable', 'integer'],
            'id_programa'         => ['nullable', 'integer'],
            'id_tipoprograma'     => ['nullable', 'integer'],
            'estado'              => ['nullable', 'integer'],
        ];
    }
}
