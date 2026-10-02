<?php

namespace App\Http\Requests\Zoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateZoomCuentaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'        => ['sometimes', 'string', 'max:100'],
            'account_id'    => ['sometimes', 'string', 'max:100'],
            'client_id'     => ['sometimes', 'string', 'max:100'],
            'client_secret' => ['sometimes', 'string', 'max:200'],
            'timezone'      => ['nullable', 'string', 'max:50'],
            'descripcion'   => ['nullable', 'string', 'max:200'],
            'activa'        => ['nullable', 'boolean'],
        ];
    }
}
