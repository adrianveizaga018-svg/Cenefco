<?php

namespace App\Http\Requests\Usuarios;

use App\Http\Requests\Concerns\ValidaEscaladaPermisos;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateUsuarioRequest extends FormRequest
{
    use ValidaEscaladaPermisos;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('usuario') ?? $this->route('id');

        return [
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', "unique:usuarios,email,{$id}"],
            'password' => ['nullable', 'string', 'min:8'],
            'tipo'     => ['nullable', 'string', 'in:admin,coordinador,docente,participante'],
            'rol_id'   => ['nullable', 'integer', 'exists:roles,id'],
            'activo'   => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $id = $this->route('usuario') ?? $this->route('id');
            $rolIdActual = DB::table('usuarios_roles')->where('usuario_id', $id)->value('rol_id');
            $rolIdNuevo = $this->input('rol_id');

            $msg = $this->rolExcedePermisosDelSolicitante(
                $rolIdNuevo ? (int) $rolIdNuevo : null,
                $rolIdActual ? (int) $rolIdActual : null,
            );
            if ($msg) {
                $validator->errors()->add('rol_id', $msg);
            }
        });
    }
}
