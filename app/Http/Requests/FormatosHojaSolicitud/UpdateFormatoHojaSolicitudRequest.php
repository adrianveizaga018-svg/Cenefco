<?php

namespace App\Http\Requests\FormatosHojaSolicitud;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormatoHojaSolicitudRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'contenido'   => 'nullable|string',
        ];
    }
}
