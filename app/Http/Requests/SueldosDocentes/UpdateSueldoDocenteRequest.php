<?php

namespace App\Http\Requests\SueldosDocentes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSueldoDocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_us'       => ['required', 'integer'],
            'concepto'    => ['required', 'string', 'max:300'],
            'monto_total' => ['required', 'numeric', 'min:0'],
            'periodo'     => ['nullable', 'string', 'max:20'],
            'gestion'     => ['nullable', 'integer'],
            'id_imp'      => ['nullable', 'integer'],
            'observacion' => ['nullable', 'string'],
            'archivo_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
