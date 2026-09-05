<?php

namespace App\Http\Requests\ArchivosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArchivoAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'url'         => 'nullable|string|max:500',
            'id_tipo'     => 'nullable|integer',
        ];
    }
}
