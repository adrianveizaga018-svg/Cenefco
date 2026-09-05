<?php

namespace App\Http\Requests\Resenas;

use Illuminate\Foundation\Http\FormRequest;

class StoreResenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'programa_id'   => ['required', 'integer', 'min:1'],
            'usuario_id'    => ['nullable', 'integer'],
            'nombre'        => ['required', 'string', 'max:200'],
            'cargo_actual'  => ['nullable', 'string', 'max:200'],
            'foto_url'      => ['nullable', 'string', 'max:500'],
            'calificacion'  => ['required', 'integer', 'min:1', 'max:5'],
            'titulo_resena' => ['nullable', 'string', 'max:300'],
            'resena'        => ['required', 'string'],
            'destacada'     => ['nullable', 'boolean'],
        ];
    }
}
