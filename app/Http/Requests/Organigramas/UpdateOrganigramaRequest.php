<?php

namespace App\Http\Requests\Organigramas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganigramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'secretaria_id' => ['sometimes', 'required', 'integer', 'exists:secretarias,id'],
            'parent_id' => ['nullable', 'integer', 'exists:organigrama,id'],
            'nivel' => ['nullable', 'integer', 'min:0'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'imagen_url' => ['nullable', 'string', 'max:255'],
            'fecha_actualizacion' => ['nullable', 'date'],
            'vigente' => ['nullable', 'boolean'],
        ];
    }
}
