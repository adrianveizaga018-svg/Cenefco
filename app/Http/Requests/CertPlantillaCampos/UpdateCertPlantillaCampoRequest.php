<?php

namespace App\Http\Requests\CertPlantillaCampos;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertPlantillaCampoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'clave'      => ['sometimes', 'required', 'string', 'max:100'],
            'etiqueta'   => ['nullable', 'string', 'max:200'],
            'tipo'       => ['nullable', 'string', 'max:50'],
            'pos_x_pct'  => ['nullable', 'numeric'],
            'pos_y_pct'  => ['nullable', 'numeric'],
            'ancho_pct'  => ['nullable', 'numeric'],
            'alto_pct'   => ['nullable', 'numeric'],
            'fuente'     => ['nullable', 'string', 'max:100'],
            'tamano_pt'  => ['nullable', 'integer'],
            'color'      => ['nullable', 'string', 'max:7'],
            'alineacion' => ['nullable', 'in:left,center,right'],
            'negrita'    => ['nullable', 'boolean'],
            'cursiva'    => ['nullable', 'boolean'],
            'mayusculas' => ['nullable', 'string', 'max:20'],
            'valor_fijo' => ['nullable', 'string', 'max:300'],
            'activo'     => ['nullable', 'boolean'],
            'orden'      => ['nullable', 'integer'],
        ];
    }
}
