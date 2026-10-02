<?php

namespace App\Http\Requests\UsuariosMoodle;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioMoodleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_moodle'      => ['nullable', 'integer'],
            'moodle_id_user' => ['nullable', 'string', 'max:200'],
            'estado'         => ['nullable', 'integer'],
        ];
    }
}
