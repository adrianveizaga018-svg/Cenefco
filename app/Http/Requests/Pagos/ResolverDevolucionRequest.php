<?php

namespace App\Http\Requests\Pagos;

use Illuminate\Foundation\Http\FormRequest;

class ResolverDevolucionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'estado'         => ['required', 'string', 'in:cancelada,aprobada,rechazada'],
            'nota_respuesta' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
