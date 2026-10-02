<?php

namespace App\Http\Requests\BloquesPlantilla;

use Illuminate\Foundation\Http\FormRequest;

class StoreBloquePlantillaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_bloqueplantilla' => 'required|integer',
            'nombre'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string|max:500',
            'tipo'               => 'nullable|string|max:100',
        ];
    }
}
