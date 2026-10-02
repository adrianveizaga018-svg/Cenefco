<?php

namespace App\Http\Requests\Articulos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticuloRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo'               => ['sometimes', 'required', 'string', 'max:200'],
            'slug'                 => ['nullable', 'string', 'max:300'],
            'entradilla'           => ['nullable', 'string', 'max:500'],
            'contenido'            => ['nullable', 'string'],
            'imagen_principal_url' => ['nullable', 'string', 'max:255'],
            'imagen_alt'           => ['nullable', 'string', 'max:255'],
            'destacada'            => ['nullable', 'boolean'],
            'fecha_publicacion'    => ['nullable', 'date'],
            'estado_web'           => ['nullable', 'in:borrador,publicado,archivado'],
            'meta_titulo'          => ['nullable', 'string', 'max:300'],
            'meta_descripcion'     => ['nullable', 'string', 'max:500'],
            'estado'               => ['nullable', 'integer'],
            'etiquetas'            => ['nullable', 'array'],
            'etiquetas.*'          => ['integer'],
        ];
    }
}
