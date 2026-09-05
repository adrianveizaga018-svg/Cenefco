<?php

namespace App\Http\Requests\Cartas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'num_carta'    => 'sometimes|string|max:50',
            'id_us'        => 'sometimes|integer',
            'id_plan'      => 'sometimes|integer',
            'nombresenor'  => 'sometimes|nullable|string|max:255',
            'nombretitulo' => 'sometimes|nullable|string|max:100',
            'textocarta1'  => 'sometimes|nullable|string',
            'textocarta2'  => 'sometimes|nullable|string',
            'textocarta3'  => 'sometimes|nullable|string',
        ];
    }
}
