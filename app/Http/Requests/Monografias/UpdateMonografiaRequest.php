<?php

namespace App\Http\Requests\Monografias;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonografiaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo_monografia'      => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion_monografia' => ['nullable', 'string'],
            'fecha_publicacion'      => ['nullable', 'date'],
            'autor'                  => ['nullable', 'string', 'max:200'],
            'archivo'                => ['nullable', 'string', 'max:200'],
            'estado'                 => ['nullable', 'integer'],
        ];
    }
}
