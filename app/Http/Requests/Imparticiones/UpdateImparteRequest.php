<?php

namespace App\Http\Requests\Imparticiones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImparteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'periodo'            => ['nullable', 'string', 'max:30'],
            'gestion'            => ['nullable', 'string', 'max:10'],
            'id_us'              => ['nullable', 'integer', 'min:1'],
            'id_mat'             => ['nullable', 'integer', 'min:1'],
            'paralelo'           => ['nullable', 'string', 'max:200'],
            'cupo'               => ['nullable', 'string', 'max:200'],
            'observacion_imp'    => ['nullable', 'string'],
            'nro_resolucion_hcu' => ['nullable', 'string', 'max:200'],
            'estado'             => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
