<?php

namespace App\Http\Requests\Faqs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'pregunta'    => ['sometimes', 'required', 'string', 'max:500'],
            'respuesta'   => ['sometimes', 'required', 'string'],
            'categoria'   => ['nullable', 'string', 'max:100'],
            'programa_id' => ['nullable', 'integer'],
            'orden'       => ['nullable', 'integer'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }
}
