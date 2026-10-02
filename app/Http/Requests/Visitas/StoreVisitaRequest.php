<?php

namespace App\Http\Requests\Visitas;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id'   => ['required', 'string', 'max:36'],
            'url'          => ['required', 'string', 'max:2000'],
            'ruta'         => ['required', 'string', 'max:500'],
            'titulo'       => ['nullable', 'string', 'max:300'],
            'referrer'     => ['nullable', 'string', 'max:2000'],
            'pais'         => ['nullable', 'string', 'max:100'],
            'ciudad'       => ['nullable', 'string', 'max:100'],
            'dispositivo'  => ['nullable', 'string', 'in:mobile,tablet,desktop'],
            'navegador'    => ['nullable', 'string', 'max:50'],
            'so'           => ['nullable', 'string', 'max:50'],
            'duracion_seg' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
