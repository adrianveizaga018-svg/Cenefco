<?php

namespace App\Http\Requests\ArchivosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreArchivoAcademicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_arch'     => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'url'         => 'required|string|max:500',
            'id_tipo'     => 'nullable|integer',
        ];
    }
}
