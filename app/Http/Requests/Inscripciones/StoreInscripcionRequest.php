<?php

namespace App\Http\Requests\Inscripciones;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_us_reg'       => ['nullable', 'integer'],
            'fecha_ins'       => ['nullable', 'date'],
            'id_us'           => ['required', 'integer', 'min:1', 'exists:t_usuario,id_us'],
            'id_imp'          => ['required', 'integer', 'min:1', 'exists:t_imparte,id_imp'],
            'id_plan'         => ['nullable', 'integer', 'min:1'],
            'observacion_ins' => ['nullable', 'string'],
            'observacion'     => ['nullable', 'string'],
            'periodo'         => ['nullable', 'string', 'max:30'],
            'gestion'         => ['nullable', 'string', 'max:10'],
            'estado'          => ['nullable', 'integer', 'in:0,1'],
            'id_vendedor'     => ['nullable', 'integer', 'min:1'],
            'canal_venta'     => ['nullable', 'string', 'in:admin,portal,whatsapp,referido'],
        ];
    }
}
