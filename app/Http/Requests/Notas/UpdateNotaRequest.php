<?php

namespace App\Http\Requests\Notas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nota'             => ['sometimes', 'required', 'integer'],
            'nota_seg'         => ['nullable', 'integer'],
            'paralelo'         => ['nullable', 'string', 'max:200'],
            'mostrarcert_notas' => ['nullable', 'integer'],
            'estado'           => ['nullable', 'integer'],
        ];
    }
}
