<?php

namespace App\Http\Requests\FormulariosIns;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormularioInsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_imp'      => 'nullable|integer',
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ];
    }
}
