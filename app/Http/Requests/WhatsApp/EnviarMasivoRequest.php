<?php

namespace App\Http\Requests\WhatsApp;

use Illuminate\Foundation\Http\FormRequest;

class EnviarMasivoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phones'   => ['required', 'array', 'min:1'],
            'phones.*' => ['required', 'string'],
            'mensaje'  => ['required', 'string'],
        ];
    }
}
