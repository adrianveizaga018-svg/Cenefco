<?php

namespace App\Http\Requests\Etiquetas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEtiquetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:80', "unique:web_etiqueta,nombre,{$id}"],
        ];
    }
}
