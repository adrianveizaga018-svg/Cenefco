<?php

namespace App\Http\Requests\WhatsApp;

use Illuminate\Foundation\Http\FormRequest;

class AsignarEtiquetasRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'etiqueta_ids'   => ['required', 'array'],
            'etiqueta_ids.*' => ['integer', 'exists:whatsapp_etiquetas,id'],
        ];
    }
}
