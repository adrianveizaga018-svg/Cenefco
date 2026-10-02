<?php

namespace App\Http\Requests\RedesSociales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRedSocialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function prepareForValidation(): void
    {
        if ($this->has('red')) {
            $this->merge(['red' => strtolower($this->red)]);
        }
    }

    public function rules(): array
    {
        return [
            'red'            => ['required', 'string', 'max:50', Rule::unique('web_redes_sociales', 'red')],
            'nombre_display' => ['nullable', 'string', 'max:100'],
            'url'            => ['required', 'url', 'max:255'],
            'icono_clase'    => ['nullable', 'string', 'max:100'],
            'pixel_id'       => ['nullable', 'string', 'max:100'],
            'mostrar_footer' => ['nullable', 'boolean'],
            'mostrar_header' => ['nullable', 'boolean'],
            'activo'         => ['nullable', 'boolean'],
            'orden'          => ['nullable', 'integer'],
        ];
    }
}
