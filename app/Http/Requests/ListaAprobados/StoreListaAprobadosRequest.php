<?php

namespace App\Http\Requests\ListaAprobados;

use Illuminate\Foundation\Http\FormRequest;

class StoreListaAprobadosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'imparte_id'         => ['required', 'integer'],
            'usuario_id'         => ['required', 'integer'],
            'inscripcion_id'     => ['nullable', 'integer'],
            'nota_final'         => ['nullable', 'numeric'],
            'nota_minima'        => ['nullable', 'numeric'],
            'condicion'          => ['nullable', 'string', 'in:aprobado,reprobado,retirado'],
            'observacion'        => ['nullable', 'string', 'max:500'],
            'comprobante_url'    => ['nullable', 'string', 'max:500'],
            'ajuste_manual'      => ['nullable', 'boolean'],
            'estado_certificado' => ['nullable', 'string', 'in:pendiente,generado,entregado,anulado'],
            'registrado_por'     => ['nullable', 'integer'],
            'id_us_reg'          => ['nullable', 'integer'],
        ];
    }
}
