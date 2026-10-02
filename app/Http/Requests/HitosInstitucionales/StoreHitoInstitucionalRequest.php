<?php

namespace App\Http\Requests\HitosInstitucionales;

use Illuminate\Foundation\Http\FormRequest;

class StoreHitoInstitucionalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'anio'        => ['required', 'string', 'max:10'],
            'titulo'      => ['required', 'string', 'max:300'],
            'descripcion' => ['nullable', 'string'],
            'imagen_url'  => ['nullable', 'string', 'max:255'],
            'imagen_alt'  => ['nullable', 'string', 'max:255'],
            'orden'       => ['nullable', 'integer'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }
}
