<?php

namespace App\Http\Requests\FuncionalidadesForms;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFuncionalidadFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_regform'  => 'nullable|integer',
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ];
    }
}
