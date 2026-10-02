<?php

namespace App\Http\Requests\CartaGens;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartaGenRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_cartagen'                  => 'required|integer',
            'id_us_reg'                    => 'required|integer',
            'num_carta'                    => 'required|string|max:50',
            'id_us'                        => 'required|integer',
            'id_cartamod'                  => 'required|integer',
            'textocarta'                   => 'nullable|string',
            'textocarta1'                  => 'nullable|string',
            'textocarta3'                  => 'nullable|string',
            'usar_encabezado_pie_estandar' => 'nullable|boolean',
            'cp_nro_contrato'              => 'nullable|string|max:50',
            'cp_gestion_contrato'          => 'nullable|string|max:10',
        ];
    }
}
