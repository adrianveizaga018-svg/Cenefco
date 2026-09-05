<?php

namespace App\Http\Requests\DocentesPerfil;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocentePerfilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_completo'  => ['sometimes', 'required', 'string', 'max:300'],
            'usuario_id'       => ['nullable', 'integer'],
            'titulo_academico' => ['nullable', 'string', 'max:200'],
            'especialidad'     => ['nullable', 'string', 'max:300'],
            'biografia'        => ['nullable', 'string'],
            'foto_url'         => ['nullable', 'string', 'max:255'],
            'foto_alt'         => ['nullable', 'string', 'max:255'],
            'email_publico'    => ['nullable', 'email', 'max:100'],
            'telefono'         => ['nullable', 'string', 'max:30'],
            'linkedin_url'     => ['nullable', 'string', 'max:255'],
            'twitter_url'      => ['nullable', 'string', 'max:255'],
            'sitio_web_url'    => ['nullable', 'string', 'max:255'],
            'tipo'             => ['nullable', 'string', 'max:100'],
            'mostrar_en_web'   => ['nullable', 'boolean'],
            'orden'            => ['nullable', 'integer'],
            'estado'           => ['nullable', 'string', 'in:publicado,borrador,archivado'],
        ];
    }
}
