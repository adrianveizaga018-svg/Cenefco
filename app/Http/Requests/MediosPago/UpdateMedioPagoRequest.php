<?php

namespace App\Http\Requests\MediosPago;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedioPagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:255',
            'orden'  => 'sometimes|nullable|integer',
            'activo' => 'sometimes|boolean',
        ];
    }
}
