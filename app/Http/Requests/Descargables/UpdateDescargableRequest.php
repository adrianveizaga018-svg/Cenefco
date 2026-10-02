<?php

declare(strict_types=1);

namespace App\Http\Requests\Descargables;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDescargableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'             => ['sometimes', 'nullable', 'string', 'max:300'],
            'tipo'               => ['sometimes', 'nullable', 'string', 'max:100'],
            'archivo_url'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'imagen_portada_url' => ['sometimes', 'nullable', 'string', 'max:500'],
            'programa_id'        => ['sometimes', 'nullable', 'integer'],
            'requiere_datos'     => ['sometimes', 'nullable', 'boolean'],
            'orden'              => ['sometimes', 'nullable', 'integer'],
            'activo'             => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
