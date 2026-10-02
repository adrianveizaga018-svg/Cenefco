<?php

namespace App\Http\Requests\HojasEvaluacion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHojaEvaluacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_us'       => 'nullable|integer',
            'id_us_tut'   => 'nullable|integer',
            'descripcion' => 'nullable|string',
            'fecha'       => 'nullable|date',
            'puntaje'     => 'nullable|numeric',
        ];
    }
}
