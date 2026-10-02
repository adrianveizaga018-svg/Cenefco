<?php

namespace App\Http\Requests\Planillas;

use Illuminate\Foundation\Http\FormRequest;

class GenerarPlanillaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'anio' => ['required', 'integer', 'min:2020', 'max:2100'],
            'mes'  => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}
