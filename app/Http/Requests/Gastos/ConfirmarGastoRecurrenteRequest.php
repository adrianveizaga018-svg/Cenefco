<?php

namespace App\Http\Requests\Gastos;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarGastoRecurrenteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha'       => ['required', 'date'],
            'comprobante' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
