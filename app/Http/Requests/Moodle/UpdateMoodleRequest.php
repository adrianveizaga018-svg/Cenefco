<?php

namespace App\Http\Requests\Moodle;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMoodleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo_moodle'        => 'nullable|string|max:200',
            'cp_moodle_servidor'   => 'nullable|string|max:200',
            'cp_moodle_base_datos' => 'nullable|string|max:200',
            'cp_moodle_usuario_bd' => 'nullable|string|max:200',
            'cp_moodle_contrasena' => 'nullable|string|max:200',
            'cp_url_campus'        => 'nullable|string|max:200',
            'estado'               => 'nullable|integer',
        ];
    }
}
