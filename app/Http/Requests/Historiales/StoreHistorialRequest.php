<?php

namespace App\Http\Requests\Historiales;

use Illuminate\Foundation\Http\FormRequest;

class StoreHistorialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_historial'      => 'required|integer',
            'id_us'             => 'required|integer',
            'id_tiporeferencia' => 'nullable|integer',
            'id_tipohistorial'  => 'nullable|integer',
            'descripcion'       => 'nullable|string',
            'fecha'             => 'nullable|date',
        ];
    }
}
