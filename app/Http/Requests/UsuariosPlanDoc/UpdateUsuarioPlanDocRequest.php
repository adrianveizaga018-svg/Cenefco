<?php

namespace App\Http\Requests\UsuariosPlanDoc;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioPlanDocRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_plandoc' => ['nullable', 'integer'],
            'estado'     => ['nullable', 'integer'],
        ];
    }
}
