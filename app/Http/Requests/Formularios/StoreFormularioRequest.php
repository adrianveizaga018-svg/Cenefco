<?php

namespace App\Http\Requests\Formularios;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormularioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'        => ['required', 'string', 'max:200'],
            'slug'          => ['nullable', 'string', 'max:250', 'unique:web_formulario,slug'],
            'descripcion'   => ['nullable', 'string'],
            'campos'        => ['nullable', 'array'],
            'campos.*.nombre_campo'  => ['required_with:campos', 'string', 'max:100'],
            'campos.*.etiqueta'      => ['required_with:campos', 'string', 'max:200'],
            'campos.*.tipo'          => ['required_with:campos', 'string', 'in:text,email,tel,number,date,select,textarea,file,checkbox'],
            'campos.*.requerido'     => ['nullable', 'boolean'],
            'campos.*.opciones'      => ['nullable', 'array'],
            'campos.*.validacion'                => ['nullable', 'array'],
            'campos.*.validacion.min'             => ['nullable', 'numeric'],
            'campos.*.validacion.max'             => ['nullable', 'numeric'],
            'campos.*.validacion.min_length'      => ['nullable', 'integer', 'min:0'],
            'campos.*.validacion.max_length'       => ['nullable', 'integer', 'min:1'],
            'campos.*.validacion.patron'          => ['nullable', 'string', 'max:500'],
            'campos.*.validacion.extensiones'     => ['nullable', 'array'],
            'campos.*.validacion.extensiones.*'   => ['string', 'max:10'],
            'campos.*.validacion.tamano_max_mb'   => ['nullable', 'numeric', 'min:0.1', 'max:20'],
            'activo'        => ['nullable', 'boolean'],
        ];
    }
}
