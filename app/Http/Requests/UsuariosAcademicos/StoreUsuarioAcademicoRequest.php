<?php

namespace App\Http\Requests\UsuariosAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_us'           => ['required', 'integer', 'min:1'],
            'id_us_reg'       => ['nullable', 'integer', 'min:1'],
            'tipoestudiante'  => ['nullable', 'string', 'max:200'],
            'nombre_usuario'  => ['nullable', 'string', 'max:100'],
            'categoria'       => ['nullable', 'string', 'max:200'],
            'titulo_academico' => ['nullable', 'string', 'max:200'],
            'appaterno'       => ['nullable', 'string', 'max:100'],
            'apmaterno'       => ['nullable', 'string', 'max:200'],
            'nombre'          => ['required', 'string', 'max:100'],
            'ci'              => ['nullable', 'string', 'max:200'],
            'expedido'        => ['nullable', 'integer'],
            'telefono'        => ['nullable', 'string', 'max:20'],
            'celular'         => ['nullable', 'string', 'max:20'],
            'genero'          => ['nullable', 'integer'],
            'email'           => ['nullable', 'email', 'max:100'],
            'direccion'       => ['nullable', 'string', 'max:255'],
            'ciudad'          => ['nullable', 'string', 'max:120'],
            'pais'            => ['nullable', 'string', 'max:200'],
            'id_universidad'  => ['nullable', 'integer', 'min:1'],
            'id_carrera'      => ['nullable', 'integer', 'min:1'],
            'estado'          => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
