<?php

namespace App\Http\Requests\Testimonios;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'       => ['sometimes', 'required', 'string', 'max:200'],
            'testimonio'   => ['sometimes', 'required', 'string'],
            'cargo'        => ['nullable', 'string', 'max:200'],
            'empresa'      => ['nullable', 'string', 'max:200'],
            'calificacion' => ['nullable', 'integer', 'min:1', 'max:5'],
            'foto_url'     => ['nullable', 'string', 'max:255'],
            'foto_alt'     => ['nullable', 'string', 'max:255'],
            'programa_id'  => ['nullable', 'integer'],
            'destacado'    => ['nullable', 'boolean'],
            'orden'        => ['nullable', 'integer'],
            'estado'       => ['nullable', 'string', 'in:publicado,borrador,archivado'],
        ];
    }
}
