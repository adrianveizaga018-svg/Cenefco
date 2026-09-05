<?php

namespace App\Http\Requests\RedesSociales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRedSocialRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'red'            => ['sometimes', 'required', 'string', 'max:50', Rule::unique('web_redes_sociales', 'red')->ignore($id)],
            'nombre_display' => ['nullable', 'string', 'max:100'],
            'url'            => ['sometimes', 'required', 'url', 'max:255'],
            'icono_clase'    => ['nullable', 'string', 'max:100'],
            'pixel_id'       => ['nullable', 'string', 'max:100'],
            'mostrar_footer' => ['nullable', 'boolean'],
            'mostrar_header' => ['nullable', 'boolean'],
            'activo'         => ['nullable', 'boolean'],
            'orden'          => ['nullable', 'integer'],
        ];
    }
}
