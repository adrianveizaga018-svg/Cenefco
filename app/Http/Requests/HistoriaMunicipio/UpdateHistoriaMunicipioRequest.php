<?php

namespace App\Http\Requests\HistoriaMunicipio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHistoriaMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'string', 'max:250'],
            'contenido' => ['nullable', 'string'],
            'fecha_inicio' => ['nullable', 'string', 'max:100'],
            'fecha_fin' => ['nullable', 'string', 'max:100'],
            'imagen_url' => ['nullable', 'string', 'max:500'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
