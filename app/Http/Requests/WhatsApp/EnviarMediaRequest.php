<?php

namespace App\Http\Requests\WhatsApp;

use Illuminate\Foundation\Http\FormRequest;

class EnviarMediaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phones'   => ['required', 'array', 'min:1'],
            'phones.*' => ['required', 'string'],
            'tipo'     => ['required', 'in:image,document'],
            'archivo'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'caption'  => ['nullable', 'string', 'max:1024'],
            'filename' => ['nullable', 'string', 'max:255'],
        ];
    }
}
