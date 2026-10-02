<?php

namespace App\Http\Requests\Comisiones;

use Illuminate\Foundation\Http\FormRequest;

class PagarComisionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'comprobante_pago' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }
}
