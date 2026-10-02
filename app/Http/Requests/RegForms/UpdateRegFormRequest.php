<?php

namespace App\Http\Requests\RegForms;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_regcp'   => 'nullable|integer',
            'id_niv'     => 'nullable|integer',
            'nombre'     => 'nullable|string|max:255',
            'descripcion'=> 'nullable|string|max:500',
        ];
    }
}
