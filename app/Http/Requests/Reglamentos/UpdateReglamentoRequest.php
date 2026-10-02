<?php

namespace App\Http\Requests\Reglamentos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReglamentoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'bienvenida'         => 'sometimes|nullable|string',
            'reglas_asistencia'  => 'sometimes|nullable|string',
            'reglas_evaluacion'  => 'sometimes|nullable|string',
            'reglas_pagos'       => 'sometimes|nullable|string',
            'reglas_conducta'    => 'sometimes|nullable|string',
            'reglas_plataformas' => 'sometimes|nullable|string',
            'reglas_derechos'    => 'sometimes|nullable|string',
        ];
    }
}
