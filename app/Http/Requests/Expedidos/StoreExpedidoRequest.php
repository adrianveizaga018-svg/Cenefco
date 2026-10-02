<?php

namespace App\Http\Requests\Expedidos;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpedidoRequest extends FormRequest
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
