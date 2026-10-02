<?php

namespace App\Http\Requests\PaginasAcademicas;

use Illuminate\Foundation\Http\FormRequest;

class StorePaginaAcademicaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_pagina'   => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'url'         => 'nullable|string|max:500',
            'id_mod'      => 'nullable|integer',
        ];
    }
}
