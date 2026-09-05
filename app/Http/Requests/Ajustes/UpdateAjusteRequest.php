<?php

namespace App\Http\Requests\Ajustes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor' => ['required'],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('valor') && ! is_string($this->valor)) {
            $this->merge(['valor' => json_encode($this->valor)]);
        }
    }
}
