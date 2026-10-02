<?php

namespace App\Http\Requests\ConfiguracionesAcademicas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionAcademicaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'gestion'     => 'nullable|integer',
            'id_plan'     => 'nullable|integer',
            'descripcion' => 'nullable|string|max:255',
        ];
    }
}
