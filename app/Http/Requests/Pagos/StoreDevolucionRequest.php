<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevolucionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'monto'         => ['required', 'numeric', 'min:0.01'],
            'motivo'        => ['required', 'string', 'max:1000'],
            'documento_url' => ['nullable', 'string', 'max:500'],
        ];
    }
}
