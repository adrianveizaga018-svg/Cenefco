<?php

namespace App\Http\Requests\MediosPago;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedioPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'orden'  => 'nullable|integer',
            'activo' => 'nullable|boolean',
        ];
    }
}
