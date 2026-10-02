<?php

namespace App\Http\Requests\EventosFotos;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evento_id' => ['required', 'exists:eventos,id'],
            'archivo_url' => ['required', 'string', 'max:255'],
            'titulo' => ['nullable', 'string', 'max:100'],
            'orden' => ['nullable', 'integer'],
        ];
    }
}
