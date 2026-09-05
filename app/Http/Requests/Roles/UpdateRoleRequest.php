<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\Concerns\ValidaEscaladaPermisos;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateRoleRequest extends FormRequest
{
    use ValidaEscaladaPermisos;

    private const NOMBRES_RESERVADOS = ['vendedor', 'admin'];

    private function esNombreReservado(?string $nombre): bool
    {
        if ($nombre === null) {
            return false;
        }

        return in_array(strtolower($nombre), self::NOMBRES_RESERVADOS, true);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('role') ?? $this->route('id');

        return [
            'nombre'                 => ['required', 'string', 'max:100', "unique:roles,nombre,{$id}"],
            'descripcion'            => ['nullable', 'string', 'max:255'],
            'permisos'               => ['nullable', 'array'],
            'permisos.*'             => ['string'],
            'activo'                 => ['boolean'],
            'restringido_a_vendedor' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $id = $this->route('role') ?? $this->route('id');
            $nombreActual = DB::table('roles')->where('id', $id)->value('nombre');

            if ($this->esNombreReservado($nombreActual) && $this->nombre !== $nombreActual) {
                $validator->errors()->add(
                    'nombre',
                    "El rol \"{$nombreActual}\" es reservado por el sistema y no puede renombrarse."
                );
            }

            $permisosActuales = DB::table('roles_permisos')
                ->where('rol_id', $id)
                ->join('permisos', 'permisos.id', '=', 'roles_permisos.permiso_id')
                ->pluck('permisos.codigo')
                ->all();

            $noAutorizados = $this->permisosAgregadosNoAutorizados($permisosActuales, $this->input('permisos', []));
            if (! empty($noAutorizados)) {
                $validator->errors()->add(
                    'permisos',
                    'No puedes agregar permisos que tú mismo no tienes: '.implode(', ', $noAutorizados).'.'
                );
            }
        });
    }
}
