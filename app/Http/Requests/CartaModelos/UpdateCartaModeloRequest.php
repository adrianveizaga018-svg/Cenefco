<?php

namespace App\Http\Requests\CartaModelos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartaModeloRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'num_cartamod'                  => 'sometimes|string|max:50',
            'nombremodelo'                  => 'sometimes|string|max:255',
            'textocarta'                    => 'sometimes|nullable|string',
            'textocarta1'                   => 'sometimes|nullable|string',
            'textocarta3'                   => 'sometimes|nullable|string',
            'texto_carta'                   => 'sometimes|nullable|string',
            'usar_encabezado_pie_estandar'  => 'sometimes|nullable|boolean',
        ];
    }
}
