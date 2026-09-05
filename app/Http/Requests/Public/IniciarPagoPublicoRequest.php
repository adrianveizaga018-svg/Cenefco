<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class IniciarPagoPublicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_programa'  => ['required', 'integer', 'min:1'],
            'nombre'       => ['required', 'string', 'max:100'],
            'appaterno'    => ['required', 'string', 'max:100'],
            'ci'           => ['required', 'string', 'max:30'],
            'expedido'     => ['nullable', 'string', 'max:10'],
            'email'        => ['required', 'email', 'max:200'],
            'telefono'     => ['nullable', 'string', 'max:30'],
            'id_fechapago' => ['nullable', 'integer', 'min:1'],
            'monto'        => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'success_url'  => ['required', 'url', 'max:500'],
            'cancel_url'   => ['required', 'url', 'max:500'],
        ];
    }
}
