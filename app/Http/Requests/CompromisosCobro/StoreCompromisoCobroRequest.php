<?php

namespace App\Http\Requests\CompromisosCobro;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompromisoCobroRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_ins'             => ['required', 'integer'],
            'fecha_compromiso'   => ['required', 'date', 'after_or_equal:today'],
            'hora_compromiso'    => ['nullable', 'date_format:H:i'],
            'monto_comprometido' => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'observacion'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_compromiso.after_or_equal' => 'La fecha comprometida no puede ser en el pasado.',
        ];
    }
}
