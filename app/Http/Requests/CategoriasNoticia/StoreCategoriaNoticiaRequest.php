<?php

namespace App\Http\Requests\CategoriasNoticia;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaNoticiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:categorias_noticia,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'color_hex' => ['nullable', 'string', 'max:50'],
            'activa' => ['nullable', 'boolean'],
        ];
    }
}
