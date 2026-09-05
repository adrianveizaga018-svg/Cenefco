<?php

namespace App\Http\Requests\UsuariosPlan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_plan' => ['nullable', 'integer'],
            'estado'  => ['nullable', 'integer'],
        ];
    }
}
