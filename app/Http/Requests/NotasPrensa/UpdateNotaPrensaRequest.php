<?php

declare(strict_types=1);

namespace App\Http\Requests\NotasPrensa;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotaPrensaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'            => ['sometimes', 'nullable', 'string', 'max:300'],
            'medio'             => ['sometimes', 'nullable', 'string', 'max:200'],
            'logo_medio_url'    => ['sometimes', 'nullable', 'string', 'url'],
            'logo_medio_alt'    => ['sometimes', 'nullable', 'string'],
            'resumen'           => ['sometimes', 'nullable', 'string'],
            'url_noticia'       => ['sometimes', 'nullable', 'string', 'url', 'max:500'],
            'fecha_publicacion' => ['sometimes', 'nullable', 'date'],
            'destacada'         => ['sometimes', 'nullable', 'boolean'],
            'orden'             => ['sometimes', 'nullable', 'integer'],
            'activo'            => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
