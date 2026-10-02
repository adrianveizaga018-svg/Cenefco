<?php

namespace App\Http\Requests\Certificados;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'lista_aprobado_id'       => ['required', 'integer'],
            'plantilla_id'            => ['required', 'integer'],
            'usuario_id'              => ['required', 'integer'],
            'imparte_id'              => ['required', 'integer'],
            'nombre_en_certificado'   => ['required', 'string', 'max:300'],
            'programa_en_certificado' => ['required', 'string', 'max:300'],
            'condicion'               => ['required', 'string', 'max:50'],
            'nota_final'              => ['nullable', 'numeric'],
            'horas_academicas'        => ['nullable', 'integer'],
            'fecha_inicio_curso'      => ['nullable', 'date'],
            'fecha_fin_curso'         => ['nullable', 'date'],
            'codigo_verificacion'     => ['required', 'string', 'max:50', 'unique:t_certificado,codigo_verificacion'],
            'qr_url'                  => ['nullable', 'string', 'max:500'],
            'archivo_url'             => ['nullable', 'string', 'max:500'],
            'archivo_miniatura_url'   => ['nullable', 'string', 'max:255'],
            'estado'                  => ['nullable', 'string', 'max:50'],
        ];
    }
}
