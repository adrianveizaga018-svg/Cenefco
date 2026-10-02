<?php

namespace App\Http\Requests\EventosFotos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'archivo_url' => ['sometimes', 'required', 'string', 'max:255'],
            'titulo' => ['nullable', 'string', 'max:100'],
            'orden' => ['sometimes', 'required', 'integer'],
        ];
    }
}
