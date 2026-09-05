<?php

namespace App\Http\Requests\CategoriasPrograma;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['required', 'string', 'max:200'],
            'slug'             => ['nullable', 'string', 'max:200', 'unique:web_categoria_programa,slug'],
            'descripcion'      => ['nullable', 'string'],
            'imagen_url'       => ['nullable', 'string'],
            'imagen_alt'       => ['nullable', 'string'],
            'icono'            => ['nullable', 'string', 'max:100'],
            'color'            => ['nullable', 'string', 'max:7'],
            'orden'            => ['nullable', 'integer'],
            'activo'           => ['nullable', 'boolean'],
            'meta_titulo'      => ['nullable', 'string'],
            'meta_descripcion' => ['nullable', 'string'],
            'tipo_programa_id' => ['nullable', 'integer', 'unique:web_categoria_programa,tipo_programa_id'],
            'comision_monto'   => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
        ];
    }
}
