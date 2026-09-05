<?php

namespace App\Http\Requests\Noticias;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoticiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_id' => ['sometimes', 'required', 'exists:categorias_noticia,id'],
            'titulo' => ['sometimes', 'required', 'string', 'max:300'],
            'entradilla' => ['nullable', 'string', 'max:500'],
            'cuerpo' => ['nullable', 'string'],
            'imagen_principal_url' => ['nullable', 'string', 'max:255'],
            'imagen_alt' => ['nullable', 'string', 'max:255'],
            'estado' => ['sometimes', 'required', 'string', 'in:borrador,publicado,privado'],
            'destacada' => ['sometimes', 'required', 'boolean'],
            'fecha_publicacion' => ['nullable', 'date'],
            'meta_titulo' => ['nullable', 'string', 'max:300'],
            'meta_descripcion' => ['nullable', 'string', 'max:500'],
            'etiquetas' => ['nullable', 'array'],
            'etiquetas.*' => ['integer', 'exists:etiquetas,id'],
        ];
    }
}
