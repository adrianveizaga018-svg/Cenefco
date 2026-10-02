<?php

namespace App\Http\Requests\Areas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'titulo'           => ['sometimes', 'required', 'string', 'max:200'],
            'slug'             => ['nullable', 'string', 'max:220', "unique:web_area,slug,{$id}"],
            'descripcion'      => ['nullable', 'string'],
            'logo_url'         => ['nullable', 'string', 'max:500'],
            'logo_alt'         => ['nullable', 'string', 'max:300'],
            'galeria'          => ['nullable', 'array'],
            'galeria.*'        => ['string', 'max:500'],
            'color'            => ['nullable', 'string', 'max:7'],
            'icono'            => ['nullable', 'string', 'max:100'],
            'orden'            => ['nullable', 'integer'],
            'activo'           => ['nullable', 'boolean'],
            'meta_titulo'      => ['nullable', 'string', 'max:300'],
            'meta_descripcion' => ['nullable', 'string', 'max:500'],
        ];
    }
}
