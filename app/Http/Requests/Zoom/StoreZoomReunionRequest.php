<?php

namespace App\Http\Requests\Zoom;

use Illuminate\Foundation\Http\FormRequest;

class StoreZoomReunionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cuenta_id'    => ['nullable', 'integer'],
            'tipo'         => ['required', 'in:unica,multisesion'],
            'tema'         => ['required_if:tipo,unica', 'nullable', 'string', 'max:255'],
            'curso'        => ['required_if:tipo,multisesion', 'nullable', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'string'],
            'duracion_min' => ['nullable', 'integer', 'min:15', 'max:480'],
            'n_sesiones'   => ['required_if:tipo,multisesion', 'nullable', 'integer', 'min:1', 'max:52'],
            'dias_entre'   => ['nullable', 'integer', 'min:1'],
        ];
    }
}
