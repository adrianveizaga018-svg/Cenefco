<?php

namespace App\Http\Requests\Historiales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHistorialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_us'             => 'nullable|integer',
            'id_tiporeferencia' => 'nullable|integer',
            'id_tipohistorial'  => 'nullable|integer',
            'descripcion'       => 'nullable|string',
            'fecha'             => 'nullable|date',
        ];
    }
}
