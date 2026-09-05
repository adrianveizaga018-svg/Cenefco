<?php

declare(strict_types=1);

namespace App\Http\Requests\NotasPrensa;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotaPrensaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'            => ['required', 'string', 'max:300'],
            'medio'             => ['required', 'string', 'max:200'],
            'fecha_publicacion' => ['required', 'date'],
            'logo_medio_url'    => ['nullable', 'string', 'url'],
            'logo_medio_alt'    => ['nullable', 'string'],
            'resumen'           => ['nullable', 'string'],
            'url_noticia'       => ['nullable', 'string', 'url', 'max:500'],
            'destacada'         => ['nullable', 'boolean'],
            'orden'             => ['nullable', 'integer'],
            'activo'            => ['nullable', 'boolean'],
        ];
    }
}
