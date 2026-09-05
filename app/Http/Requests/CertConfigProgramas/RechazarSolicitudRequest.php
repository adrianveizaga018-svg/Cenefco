<?php

namespace App\Http\Requests\CertConfigProgramas;

use Illuminate\Foundation\Http\FormRequest;

class RechazarSolicitudRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nota_admin' => ['required', 'string', 'max:1000'],
        ];
    }
}
