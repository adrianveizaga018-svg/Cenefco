<?php

namespace App\Http\Requests\Fotos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFotoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo_foto'      => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion_foto' => ['nullable', 'string'],
            'foto'             => ['nullable', 'string', 'max:200'],
            'fecha_foto'       => ['nullable', 'date'],
            'estado'           => ['nullable', 'integer'],
        ];
    }
}
