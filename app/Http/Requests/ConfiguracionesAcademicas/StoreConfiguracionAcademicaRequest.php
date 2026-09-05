<?php

namespace App\Http\Requests\ConfiguracionesAcademicas;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfiguracionAcademicaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_conf'     => 'required|integer',
            'gestion'     => 'required|integer',
            'id_plan'     => 'required|integer',
            'descripcion' => 'nullable|string|max:255',
        ];
    }
}
