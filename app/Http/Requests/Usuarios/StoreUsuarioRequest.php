<?php

namespace App\Http\Requests\Usuarios;

use App\Http\Requests\Concerns\ValidaEscaladaPermisos;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    use ValidaEscaladaPermisos;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8'],
            'tipo'     => ['nullable', 'string', 'in:admin,coordinador,docente,participante'],
            'rol_id'   => ['nullable', 'integer', 'exists:roles,id'],
            'activo'   => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $rolId = $this->input('rol_id');
            if ($msg = $this->rolExcedePermisosDelSolicitante($rolId ? (int) $rolId : null)) {
                $validator->errors()->add('rol_id', $msg);
            }
        });
    }
}
