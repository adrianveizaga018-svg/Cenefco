<?php

namespace App\Http\Requests\CartaModelos;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartaModeloRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_cartamod'                   => 'required|integer',
            'id_us_reg'                     => 'required|integer',
            'num_cartamod'                  => 'required|string|max:50',
            'nombremodelo'                  => 'required|string|max:255',
            'textocarta'                    => 'nullable|string',
            'textocarta1'                   => 'nullable|string',
            'textocarta3'                   => 'nullable|string',
            'texto_carta'                   => 'nullable|string',
            'usar_encabezado_pie_estandar'  => 'nullable|boolean',
        ];
    }
}
