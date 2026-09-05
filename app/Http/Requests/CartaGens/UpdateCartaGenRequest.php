<?php

namespace App\Http\Requests\CartaGens;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartaGenRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'num_carta'                    => 'sometimes|string|max:50',
            'id_us'                        => 'sometimes|integer',
            'id_cartamod'                  => 'sometimes|integer',
            'textocarta'                   => 'sometimes|nullable|string',
            'textocarta1'                  => 'sometimes|nullable|string',
            'textocarta3'                  => 'sometimes|nullable|string',
            'usar_encabezado_pie_estandar' => 'sometimes|nullable|boolean',
            'cp_nro_contrato'              => 'sometimes|nullable|string|max:50',
            'cp_gestion_contrato'          => 'sometimes|nullable|string|max:10',
        ];
    }
}
