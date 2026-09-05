<?php

namespace App\Http\Requests\Cartas;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_carta'     => 'required|integer',
            'id_us_reg'    => 'required|integer',
            'num_carta'    => 'required|string|max:50',
            'id_us'        => 'required|integer',
            'id_plan'      => 'required|integer',
            'nombresenor'  => 'nullable|string|max:255',
            'nombretitulo' => 'nullable|string|max:100',
            'textocarta1'  => 'nullable|string',
            'textocarta2'  => 'nullable|string',
            'textocarta3'  => 'nullable|string',
        ];
    }
}
