<?php

namespace App\Http\Requests\MensajesContacto;

use Illuminate\Foundation\Http\FormRequest;

class StoreMensajeContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nombre_remitente' => ['required', 'string', 'max:150'],
            'email_remitente' => ['required', 'email', 'max:150'],
            'telefono_remitente' => ['nullable', 'string', 'max:50'],
            'asunto' => ['required', 'string', 'max:200'],
            'mensaje' => ['required', 'string'],
            'secretaria_destino_id' => ['nullable', 'integer', 'exists:secretarias,id'],
        ];

        
        
        if (! $this->user()) {
            $rules['captcha_key'] = ['required', 'string'];
            $rules['captcha_value'] = ['required', 'captcha_api:' . $this->input('captcha_key') . ',inscripcion'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'captcha_key.required' => 'El captcha es requerido.',
            'captcha_value.required' => 'Ingrese el código de verificación.',
            'captcha_value.captcha_api' => 'El código de verificación es incorrecto.',
        ];
    }
}
