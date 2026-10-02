<?php

namespace App\Http\Requests\Imparticiones;

use Illuminate\Foundation\Http\FormRequest;

class StoreImparteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_imp'             => ['required', 'integer', 'min:1'],
            'id_us_reg'          => ['nullable', 'integer', 'min:1'],
            'num_imp'            => ['nullable', 'integer', 'min:0'],
            'periodo'            => ['nullable', 'string', 'max:30'],
            'gestion'            => ['nullable', 'string', 'max:10'],
            'id_us'              => ['nullable', 'integer', 'min:1'],
            'id_mat'             => ['nullable', 'integer', 'min:1'],
            'paralelo'           => ['nullable', 'string', 'max:200'],
            'cupo'               => ['nullable', 'string', 'max:200'],
            'observacion_imp'    => ['nullable', 'string'],
            'nro_resolucion_hcu' => ['nullable', 'string', 'max:200'],
            'id_moodle'          => ['nullable', 'integer'],
            'estado'             => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
