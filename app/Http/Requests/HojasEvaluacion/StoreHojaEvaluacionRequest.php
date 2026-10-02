<?php

namespace App\Http\Requests\HojasEvaluacion;

use Illuminate\Foundation\Http\FormRequest;

class StoreHojaEvaluacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_hojaevaluacion' => 'required|integer',
            'id_us'             => 'required|integer',
            'id_us_tut'         => 'nullable|integer',
            'descripcion'       => 'nullable|string',
            'fecha'             => 'nullable|date',
            'puntaje'           => 'nullable|numeric',
        ];
    }
}
