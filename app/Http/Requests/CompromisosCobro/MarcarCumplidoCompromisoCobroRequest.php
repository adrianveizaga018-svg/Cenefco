<?php

namespace App\Http\Requests\CompromisosCobro;

use Illuminate\Foundation\Http\FormRequest;

class MarcarCumplidoCompromisoCobroRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'observacion' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
