<?php

namespace App\Http\Requests\CertConfigProgramas;

use Illuminate\Foundation\Http\FormRequest;

class SubirComprobanteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'comprobante_url' => ['required', 'string', 'max:500'],
        ];
    }
}
