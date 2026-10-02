<?php

namespace App\Http\Requests\WebMenus;

use Illuminate\Foundation\Http\FormRequest;

class StoreWebMenuRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'activo'      => 'nullable|boolean',
        ];
    }
}
