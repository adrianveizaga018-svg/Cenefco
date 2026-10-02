<?php

namespace App\Http\Requests\CertConfigProgramas;

use Illuminate\Foundation\Http\FormRequest;

class UpsertCertConfigRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'programa_id' => ['required', 'integer', 'exists:t_programa,id_programa'],
            'activo'      => ['boolean'],
            'titulo'      => ['nullable', 'string', 'max:300'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
