<?php

namespace App\Http\Requests\Usuarios;

use App\Application\Usuarios\Services\IdentidadUnicaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => ['sometimes', 'string', 'max:100'],
            'apellido'         => ['sometimes', 'string', 'max:100'],
            'ci'               => ['sometimes', 'nullable', 'string', 'max:30'],
            'telefono'         => ['sometimes', 'nullable', 'string', 'max:20'],
            'current_password' => ['required_with:password', 'string'],
            'password'         => ['sometimes', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user     = $this->user();
            $ci       = trim((string) $this->input('ci'));
            $telefono = trim((string) $this->input('telefono'));

            if ($validator->errors()->isNotEmpty() || ($ci === '' && $telefono === '')) {
                return;
            }

            // El CI enlaza la cuenta con el historial académico: una vez fijado no se cambia desde el perfil
            if ($ci !== '' && ! empty($user->ci) && $user->ci !== $ci) {
                $validator->errors()->add('ci', 'Tu cuenta ya tiene un carnet registrado. Para corregirlo comunícate con CENEFCO.');

                return;
            }

            $errores = app(IdentidadUnicaService::class)
                ->errores($user->id, $user->email, $ci ?: $user->ci, $telefono, ! empty($user->ci));

            foreach ($errores as $campo => $mensaje) {
                $validator->errors()->add($campo, $mensaje);
            }
        });
    }
}
