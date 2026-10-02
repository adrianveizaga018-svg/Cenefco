<?php

namespace App\Http\Requests\InscripcionesDiplomado;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInscripcionDiplomadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado'                   => ['nullable', 'in:pendiente,revisado,aceptado,rechazado,contactado,inscrito'],
            'notificado'               => ['nullable', 'boolean'],
            'nombre'                   => ['sometimes', 'required', 'string', 'max:200'],
            'apellido_paterno'         => ['nullable', 'string', 'max:100'],
            'apellido_materno'         => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento'         => ['nullable', 'date'],
            'email'                    => ['sometimes', 'required', 'email', 'max:100'],
            'ci'                       => ['nullable', 'string', 'max:30'],
            'expedido_id'              => ['nullable', 'integer'],
            'telefono_grupo_inscritos' => ['nullable', 'string', 'max:20'],
            'archivo_ci'               => ['nullable', 'string', 'max:255'],
            'archivo_titulo'           => ['nullable', 'string', 'max:255'],
            'archivo_cv'               => ['nullable', 'string', 'max:255'],
            'archivo_foto_3x3'         => ['nullable', 'string', 'max:255'],
            'ciudad_residencia_id'     => ['nullable', 'integer'],
            'provincia_especificar'    => ['nullable', 'string', 'max:120'],
            'medio_pago'               => ['nullable', 'string', 'max:100'],
            'medio_pago_id'            => ['nullable', 'integer', 'exists:web_medio_pago,id'],
            'monto_pagado'             => ['nullable', 'numeric'],
            'archivo_comprobante_pago' => ['nullable', 'string', 'max:255'],
            'sugerencia_curso'         => ['nullable', 'string'],
            'recomendar_docente'       => ['nullable', 'boolean'],
            'detalle_docente'          => ['nullable', 'string'],
            'programa_id'              => ['nullable', 'integer'],
        ];
    }
}
