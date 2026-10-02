<?php

namespace App\Http\Requests\FormatosHojaSolicitud;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormatoHojaSolicitudRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_formato_hoja_solicitud' => 'required|integer',
            'nombre'                    => 'required|string|max:255',
            'descripcion'               => 'nullable|string|max:500',
            'contenido'                 => 'nullable|string',
        ];
    }
}
