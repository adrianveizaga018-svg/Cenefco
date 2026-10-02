<?php

namespace App\Http\Requests\RegForms;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_regform' => 'required|integer',
            'id_regcp'   => 'required|integer',
            'id_niv'     => 'nullable|integer',
            'nombre'     => 'required|string|max:255',
            'descripcion'=> 'nullable|string|max:500',
        ];
    }
}
