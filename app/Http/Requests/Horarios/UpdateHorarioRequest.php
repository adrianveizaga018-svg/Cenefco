<?php

namespace App\Http\Requests\Horarios;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_d'        => ['nullable', 'integer'],
            'hora_inicio' => ['nullable', 'string', 'max:8'],
            'hora_fin'    => ['nullable', 'string', 'max:8'],
            'periodos'    => ['nullable', 'string', 'max:200'],
            'estado'      => ['nullable', 'integer'],
        ];
    }
}
