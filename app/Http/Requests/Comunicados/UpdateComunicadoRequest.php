<?php

namespace App\Http\Requests\Comunicados;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComunicadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:300'],
            'resumen' => ['nullable', 'string'],
            'cuerpo' => ['nullable', 'string'],
            'imagen_url' => ['nullable', 'string', 'max:500'],
            'archivo_url' => ['nullable', 'string', 'max:500'],
            'estado' => ['nullable', 'string', 'in:borrador,publicado,archivado'],
            'destacado' => ['nullable', 'boolean'],
            'fecha_publicacion' => ['nullable', 'date'],
        ];
    }
}
