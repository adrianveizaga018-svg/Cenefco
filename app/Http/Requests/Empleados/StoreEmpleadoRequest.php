<?php

namespace App\Http\Requests\Empleados;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_completo'      => ['required', 'string', 'max:200'],
            'cargo'                => ['required', 'string', 'max:150'],
            'sueldo_mensual'       => ['required', 'numeric', 'min:0.01'],
            'ci'                   => ['required', 'string', 'max:20', 'unique:empleado,ci'],
            'carnet'               => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'correo'               => ['nullable', 'email', 'max:150'],
            'celular_personal'     => ['nullable', 'string', 'max:20'],
            'celular_corporativo'  => ['nullable', 'string', 'max:20'],
            'direccion'            => ['nullable', 'string', 'max:300'],
            'fecha_ingreso'        => ['nullable', 'date'],
            'activo'               => ['nullable', 'boolean'],
        ];
    }
}
