<?php

namespace App\Http\Requests\BannersPortal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerPortalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo'           => ['sometimes', 'nullable', 'string', 'max:150'],
            'subtitulo'        => ['nullable', 'string', 'max:150'],
            'imagen_url'       => ['sometimes', 'required', 'string', 'max:255'],
            'imagen_alt'       => ['nullable', 'string', 'max:200'],
            'imagen_movil_url' => ['nullable', 'string', 'max:255'],
            'enlace_url'       => ['nullable', 'string', 'max:255'],
            'enlace_texto'     => ['nullable', 'string', 'max:100'],
            'enlace_target'    => ['nullable', 'string', 'max:20'],
            'enlace_url_2'     => ['nullable', 'string', 'max:255'],
            'enlace_texto_2'   => ['nullable', 'string', 'max:100'],
            'posicion'         => ['nullable', 'string', 'max:50'],
            'orden'            => ['nullable', 'integer'],
            'activo'           => ['nullable', 'boolean'],
            'fecha_inicio'     => ['nullable', 'date'],
            'fecha_fin'        => ['nullable', 'date'],
        ];
    }
}
