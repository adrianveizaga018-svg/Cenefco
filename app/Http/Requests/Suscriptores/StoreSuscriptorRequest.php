<?php

declare(strict_types=1);

namespace App\Http\Requests\Suscriptores;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuscriptorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'  => ['required', 'string', 'email', 'max:100'],
            'nombre' => ['nullable', 'string'],
            'origen' => ['nullable', 'string'],
        ];
    }
}
