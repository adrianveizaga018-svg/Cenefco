<?php

namespace App\Http\Requests\RegComponentes;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegComponenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_regcp'    => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'tipo'        => 'nullable|string|max:100',
            'icono'       => 'nullable|string|max:255',
        ];
    }
}
