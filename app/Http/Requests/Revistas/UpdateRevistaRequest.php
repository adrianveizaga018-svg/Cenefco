<?php

namespace App\Http\Requests\Revistas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRevistaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo_revista'      => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion_revista' => ['nullable', 'string'],
            'fecha_publicacion'   => ['nullable', 'date'],
            'archivo'             => ['nullable', 'string', 'max:200'],
            'estado'              => ['nullable', 'integer'],
        ];
    }
}
