<?php

namespace App\Http\Requests\TiposBanco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoBancoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:150', Rule::unique('tipos_banco', 'nombre')->ignore($id)],
            'activo' => ['nullable', 'boolean'],
            'orden'  => ['nullable', 'integer'],
        ];
    }
}
