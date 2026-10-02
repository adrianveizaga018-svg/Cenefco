<?php

namespace App\Http\Requests\CategoriasCampo;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaCampoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_id' => ['required', 'integer', 'exists:web_categoria_programa,id'],
            'nombre_campo' => ['required', 'string', 'max:80'],
            'etiqueta'     => ['required', 'string', 'max:200'],
            'tipo_campo'   => ['required', 'string', 'max:30'],
            'requerido'    => ['nullable', 'boolean'],
            'orden'        => ['nullable', 'integer'],
            'paso'         => ['nullable', 'integer'],
            'activo'       => ['nullable', 'boolean'],
            'ayuda'        => ['nullable', 'string', 'max:400'],
            'opciones'     => ['nullable', 'array'],
            'validacion'   => ['nullable', 'array'],
        ];
    }
}
