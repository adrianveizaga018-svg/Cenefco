<?php

namespace App\Http\Requests\Etiquetas;

use Illuminate\Foundation\Http\FormRequest;

class StoreEtiquetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:80', 'unique:web_etiqueta,nombre'],
        ];
    }
}
