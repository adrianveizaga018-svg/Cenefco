<?php

namespace App\Http\Requests\WebMenus;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebMenuRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'activo'      => 'nullable|boolean',
        ];
    }
}
