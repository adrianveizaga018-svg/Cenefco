<?php

namespace App\Http\Requests\UsuariosMoodle;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioMoodleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_usmoodle'    => ['required', 'integer'],
            'id_us_reg'      => ['nullable', 'integer'],
            'num_usmoodle'   => ['nullable', 'integer'],
            'id_us'          => ['required', 'integer'],
            'id_moodle'      => ['required', 'integer'],
            'moodle_id_user' => ['nullable', 'string', 'max:200'],
            'estado'         => ['nullable', 'integer'],
        ];
    }
}
