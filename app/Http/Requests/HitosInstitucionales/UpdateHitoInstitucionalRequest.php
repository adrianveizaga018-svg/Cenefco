<?php

namespace App\Http\Requests\HitosInstitucionales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHitoInstitucionalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'anio'        => ['sometimes', 'required', 'string', 'max:10'],
            'titulo'      => ['sometimes', 'required', 'string', 'max:300'],
            'descripcion' => ['nullable', 'string'],
            'imagen_url'  => ['nullable', 'string', 'max:255'],
            'imagen_alt'  => ['nullable', 'string', 'max:255'],
            'orden'       => ['nullable', 'integer'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }
}
