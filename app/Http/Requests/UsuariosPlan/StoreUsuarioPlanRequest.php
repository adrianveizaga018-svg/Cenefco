<?php

namespace App\Http\Requests\UsuariosPlan;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_usuarioplan'  => ['required', 'integer'],
            'id_us'           => ['required', 'integer'],
            'id_us_reg'       => ['nullable', 'integer'],
            'num_usuarioplan' => ['nullable', 'integer'],
            'id_plan'         => ['nullable', 'integer'],
            'estado'          => ['nullable', 'integer'],
        ];
    }
}
