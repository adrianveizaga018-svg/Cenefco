<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\Concerns\ValidaEscaladaPermisos;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    use ValidaEscaladaPermisos;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'                 => ['required', 'string', 'max:100', 'unique:roles,nombre'],
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
            $noAutorizados = $this->permisosNoAutorizados($this->input('permisos', []));
            if (! empty($noAutorizados)) {
                $validator->errors()->add(
                    'permisos',
                    'No puedes asignar permisos que tú mismo no tienes: '.implode(', ', $noAutorizados).'.'
                );
            }
        });
    }
}
