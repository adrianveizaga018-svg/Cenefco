<?php

namespace App\Http\Requests\UsuariosPlanDoc;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioPlanDocRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_usuarioplandoc'  => ['required', 'integer'],
            'id_us'              => ['required', 'integer'],
            'id_us_reg'          => ['nullable', 'integer'],
            'num_usuarioplandoc' => ['nullable', 'integer'],
            'id_plandoc'         => ['nullable', 'integer'],
            'estado'             => ['nullable', 'integer'],
        ];
    }
}
