<?php

namespace App\Http\Requests\ListaAprobados;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListaAprobadosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nota_final'         => ['nullable', 'numeric'],
            'nota_minima'        => ['nullable', 'numeric'],
            'condicion'          => ['nullable', 'string', 'in:aprobado,reprobado,retirado'],
            'observacion'        => ['nullable', 'string', 'max:500'],
            'ajuste_manual'      => ['nullable', 'boolean'],
            'estado_certificado' => ['nullable', 'string', 'in:pendiente,generado,entregado,anulado'],
            'notificado_email'   => ['nullable', 'boolean'],
            'comprobante_url'    => ['nullable', 'string', 'max:500'],
        ];
    }
}
