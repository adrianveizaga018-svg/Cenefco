<?php

namespace App\Http\Requests\CitasAsesoria;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaAsesoriaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_cita_asesoria' => 'required|integer',
            'id_us'            => 'required|integer',
            'id_us_asesor'     => 'nullable|integer',
            'fecha'            => 'required|date',
            'hora'             => 'nullable|string|max:10',
            'motivo'           => 'nullable|string|max:500',
            'estado_cita'      => 'nullable|string|max:50',
        ];
    }
}
