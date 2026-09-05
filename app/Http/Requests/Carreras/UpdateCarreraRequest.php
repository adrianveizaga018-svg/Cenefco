<?php

namespace App\Http\Requests\Carreras;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_carrera' => ['sometimes', 'required', 'string', 'max:200'],
            'estado'         => ['nullable', 'integer'],
        ];
    }
}
