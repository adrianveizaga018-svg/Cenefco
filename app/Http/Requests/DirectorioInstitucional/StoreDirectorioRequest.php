<?php

namespace App\Http\Requests\DirectorioInstitucional;

use Illuminate\Foundation\Http\FormRequest;

class StoreDirectorioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'secretaria_id' => ['nullable', 'integer', 'exists:secretarias,id'],
            'nombre' => ['required', 'string', 'max:200'],
            'responsable' => ['nullable', 'string', 'max:150'],
            'cargo_responsable' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'telefono_interno' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'horario' => ['nullable', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
