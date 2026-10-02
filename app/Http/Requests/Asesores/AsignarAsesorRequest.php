<?php

namespace App\Http\Requests\Asesores;

use Illuminate\Foundation\Http\FormRequest;

class AsignarAsesorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'asesor_id' => ['required', 'integer', 'exists:web_asesor,id'],
        ];
    }
}
