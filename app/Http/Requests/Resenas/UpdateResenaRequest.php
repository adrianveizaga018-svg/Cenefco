<?php

namespace App\Http\Requests\Resenas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado'         => ['sometimes', 'required', 'in:pendiente,aprobada,rechazada'],
            'verificado'     => ['nullable', 'boolean'],
            'destacada'      => ['nullable', 'boolean'],
            'motivo_rechazo' => ['nullable', 'string', 'max:300'],
            'foto_url'       => ['nullable', 'string', 'max:500'],
            'nombre'         => ['sometimes', 'required', 'string', 'max:200'],
            'cargo_actual'   => ['nullable', 'string', 'max:200'],
            'calificacion'   => ['nullable', 'integer', 'min:1', 'max:5'],
            'titulo_resena'  => ['nullable', 'string', 'max:300'],
            'resena'         => ['sometimes', 'required', 'string'],
            'usuario_id'     => ['nullable', 'integer'],
        ];
    }
}
