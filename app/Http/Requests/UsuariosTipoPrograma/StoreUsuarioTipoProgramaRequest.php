<?php

namespace App\Http\Requests\UsuariosTipoPrograma;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioTipoProgramaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_usuariotipoprograma'  => ['required', 'integer'],
            'id_us'                   => ['required', 'integer'],
            'id_us_reg'               => ['nullable', 'integer'],
            'num_usuariotipoprograma' => ['nullable', 'integer'],
            'id_tipoprograma'         => ['nullable', 'integer'],
            'estado'                  => ['nullable', 'integer'],
        ];
    }
}
