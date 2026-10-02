<?php

namespace App\Http\Requests\CategoriasPrograma;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'nombre'           => ['nullable', 'string', 'max:200'],
            'slug'             => ['nullable', 'string', 'max:200', Rule::unique('web_categoria_programa', 'slug')->ignore($id)],
            'descripcion'      => ['nullable', 'string'],
            'imagen_url'       => ['nullable', 'string'],
            'imagen_alt'       => ['nullable', 'string'],
            'icono'            => ['nullable', 'string', 'max:100'],
            'color'            => ['nullable', 'string', 'max:7'],
            'orden'            => ['nullable', 'integer'],
            'activo'           => ['nullable', 'boolean'],
            'meta_titulo'      => ['nullable', 'string'],
            'meta_descripcion' => ['nullable', 'string'],
            'tipo_programa_id' => ['nullable', 'integer', Rule::unique('web_categoria_programa', 'tipo_programa_id')->ignore($id)],
            'comision_monto'   => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
        ];
    }
}
