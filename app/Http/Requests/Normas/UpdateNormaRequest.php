<?php

namespace App\Http\Requests\Normas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNormaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_norma_id' => ['sometimes', 'required', 'integer', 'exists:tipos_norma,id'],
            'numero' => ['sometimes', 'required', 'string', 'max:50'],
            'titulo' => ['sometimes', 'required', 'string', 'max:400'],
            'resumen' => ['nullable', 'string'],
            'texto_completo' => ['nullable', 'string'],
            'archivo_pdf_url' => ['nullable', 'string', 'max:255'],
            'fecha_promulgacion' => ['nullable', 'date'],
            'fecha_publicacion_gaceta' => ['nullable', 'date'],
            'estado_vigencia' => ['sometimes', 'required', 'string', 'in:vigente,derogada,abrogada,modificada'],
            'tema_principal' => ['nullable', 'string', 'max:100'],
            'palabras_clave' => ['nullable', 'string', 'max:500'],
        ];
    }
}
