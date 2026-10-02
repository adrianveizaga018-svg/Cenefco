<?php

namespace App\Http\Requests\FormulariosIns;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormularioInsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_formins'  => 'required|integer',
            'id_imp'      => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ];
    }
}
