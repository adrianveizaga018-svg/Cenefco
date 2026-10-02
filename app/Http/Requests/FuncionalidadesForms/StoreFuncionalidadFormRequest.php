<?php

namespace App\Http\Requests\FuncionalidadesForms;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuncionalidadFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_funcionalidadform' => 'required|integer',
            'id_regform'           => 'required|integer',
            'nombre'               => 'required|string|max:255',
            'descripcion'          => 'nullable|string|max:500',
        ];
    }
}
