<?php

namespace App\Http\Requests\Empleados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('empleado');

        return [
            'nombre_completo'      => ['sometimes', 'required', 'string', 'max:200'],
            'cargo'                => ['sometimes', 'required', 'string', 'max:150'],
            'sueldo_mensual'       => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'ci'                   => ['sometimes', 'required', 'string', 'max:20', Rule::unique('empleado', 'ci')->ignore($id)],
            'carnet'               => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'correo'               => ['nullable', 'email', 'max:150'],
            'celular_personal'     => ['nullable', 'string', 'max:20'],
            'celular_corporativo'  => ['nullable', 'string', 'max:20'],
            'direccion'            => ['nullable', 'string', 'max:300'],
            'fecha_ingreso'        => ['nullable', 'date'],
            'activo'               => ['nullable', 'boolean'],
        ];
    }
}
