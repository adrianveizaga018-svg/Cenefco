<?php

namespace App\Http\Requests\CategoriasNoticia;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaNoticiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100', "unique:categorias_noticia,nombre,{$id}"],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'color_hex' => ['nullable', 'string', 'max:50'],
            'activa' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
