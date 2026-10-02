<?php

namespace App\Http\Requests\CompromisosCobro;

use Illuminate\Foundation\Http\FormRequest;

class ReprogramarCompromisoCobroRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nueva_fecha' => ['required', 'date', 'after_or_equal:today'],
            'nueva_hora'  => ['nullable', 'date_format:H:i'],
            'motivo'      => ['required', 'string', 'in:pidio_mas_tiempo,no_respondio,promete_pagar_pronto,otro'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nueva_fecha.after_or_equal' => 'La nueva fecha no puede ser en el pasado.',
        ];
    }
}
