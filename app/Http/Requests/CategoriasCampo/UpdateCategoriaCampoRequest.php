<?php

namespace App\Http\Requests\CategoriasCampo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaCampoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'etiqueta'   => ['nullable', 'string', 'max:200'],
            'tipo_campo' => ['nullable', 'string', 'max:30'],
            'requerido'  => ['nullable', 'boolean'],
            'orden'      => ['nullable', 'integer'],
            'paso'       => ['nullable', 'integer'],
            'activo'     => ['nullable', 'boolean'],
            'ayuda'      => ['nullable', 'string', 'max:400'],
            'opciones'   => ['nullable', 'array'],
            'validacion' => ['nullable', 'array'],
        ];
    }
}
