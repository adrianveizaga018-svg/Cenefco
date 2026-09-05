<?php

declare(strict_types=1);

namespace App\Http\Requests\Descargables;

use Illuminate\Foundation\Http\FormRequest;

class StoreDescargableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'             => ['required', 'string', 'max:300'],
            'archivo_url'        => ['required', 'string', 'max:500'],
            'tipo'               => ['nullable', 'string', 'max:100'],
            'imagen_portada_url' => ['nullable', 'string', 'max:500'],
            'programa_id'        => ['nullable', 'integer'],
            'requiere_datos'     => ['nullable', 'boolean'],
            'orden'              => ['nullable', 'integer'],
            'activo'             => ['nullable', 'boolean'],
        ];
    }
}
