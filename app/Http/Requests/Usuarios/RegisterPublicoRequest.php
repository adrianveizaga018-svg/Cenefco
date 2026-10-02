<?php

namespace App\Http\Requests\Usuarios;

use App\Application\Usuarios\Services\IdentidadUnicaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterPublicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(array_map(
            fn ($valor) => is_string($valor) ? trim($valor) : $valor,
            $this->only(['email', 'ci', 'telefono']),
        ));
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'ci' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Ya existe una cuenta con ese correo. Inicia sesión o recupera tu contraseña.',
        ];
    }

    /** Se valida antes de crear la cuenta para no dejar cuentas a medias con CI o celular repetidos. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $errores = app(IdentidadUnicaService::class)
                ->errores(null, $this->input('email'), $this->input('ci'), $this->input('telefono'));

            foreach ($errores as $campo => $mensaje) {
                $validator->errors()->add($campo, $mensaje);
            }
        });
    }
}
