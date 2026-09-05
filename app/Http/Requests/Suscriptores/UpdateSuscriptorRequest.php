<?php

declare(strict_types=1);

namespace App\Http\Requests\Suscriptores;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuscriptorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'     => ['sometimes', 'nullable', 'string'],
            'confirmado' => ['sometimes', 'nullable', 'boolean'],
            'activo'     => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
