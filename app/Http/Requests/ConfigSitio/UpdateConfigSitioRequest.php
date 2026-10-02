<?php

namespace App\Http\Requests\ConfigSitio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigSitioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'              => ['sometimes', 'required', 'string', 'max:300'],
            'slogan'              => ['nullable', 'string', 'max:500'],
            'descripcion'         => ['nullable', 'string'],
            'logo_url'            => ['nullable', 'string', 'max:500'],
            'favicon_url'         => ['nullable', 'string', 'max:500'],
            'email_contacto'      => ['nullable', 'email', 'max:200'],
            'telefono'            => ['nullable', 'string', 'max:50'],
            'direccion'           => ['nullable', 'string', 'max:300'],
            'ciudad'              => ['nullable', 'string', 'max:100'],
            'pais'                => ['nullable', 'string', 'max:100'],
            'latitud'             => ['nullable', 'numeric'],
            'longitud'            => ['nullable', 'numeric'],
            'horario_atencion'    => ['nullable', 'string', 'max:300'],
            'whatsapp_numero'     => ['nullable', 'string', 'max:30'],
            'whatsapp_mensaje'    => ['nullable', 'string', 'max:300'],
            'meta_titulo'         => ['nullable', 'string', 'max:300'],
            'meta_descripcion'    => ['nullable', 'string'],
            'meta_keywords'       => ['nullable', 'string'],
            'google_analytics_id' => ['nullable', 'string', 'max:100'],
            'activo'              => ['nullable', 'boolean'],
        ];
    }
}
