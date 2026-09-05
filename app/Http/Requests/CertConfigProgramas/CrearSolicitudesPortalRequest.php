<?php

namespace App\Http\Requests\CertConfigProgramas;

use Illuminate\Foundation\Http\FormRequest;

class CrearSolicitudesPortalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'programa_id'      => ['required', 'integer'],
            'usuario_ci'       => ['required', 'string', 'max:30'],
            'usuario_nombre'   => ['required', 'string', 'max:300'],
            'usuario_email'    => ['nullable', 'email', 'max:200'],
            'inscripcion_id'   => ['nullable', 'integer'],
            'items_pago_ids'   => ['array'],
            'items_pago_ids.*' => ['integer'],
            'pagar_ahora'      => ['boolean'],
            'comprobante_url'  => ['nullable', 'string', 'max:500'],
        ];
    }
}
