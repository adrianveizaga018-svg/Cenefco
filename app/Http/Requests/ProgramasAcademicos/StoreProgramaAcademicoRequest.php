<?php

namespace App\Http\Requests\ProgramasAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramaAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_programa'              => ['required', 'integer', 'min:1'],
            'id_us_reg'                => ['nullable', 'integer', 'min:1'],
            'num_programa'             => ['nullable', 'integer', 'min:0'],
            'nombre_programa'          => ['required', 'string', 'max:200'],
            'descripcion'              => ['nullable', 'string'],
            'foto'                     => ['nullable', 'string', 'max:200'],
            'inicio_actividades'       => ['nullable', 'date'],
            'finalizacion_actividades' => ['nullable', 'date'],
            'inicio_inscripciones'     => ['nullable', 'date'],
            'titulo_documento1'        => ['nullable', 'string', 'max:200'],
            'documento1'               => ['nullable', 'string', 'max:200'],
            'titulo_documento2'        => ['nullable', 'string', 'max:200'],
            'documento2'               => ['nullable', 'string', 'max:200'],
            'titulo_documento3'        => ['nullable', 'string', 'max:200'],
            'documento3'               => ['nullable', 'string', 'max:200'],
            'titulo_documento4'        => ['nullable', 'string', 'max:200'],
            'documento4'               => ['nullable', 'string', 'max:200'],
            'dirigido'                 => ['nullable', 'string'],
            'inversion'                => ['nullable', 'string'],
            'requisitos'               => ['nullable', 'string'],
            'creditaje'                => ['nullable', 'string'],
            'objetivo'                 => ['nullable', 'string'],
            'nota'                     => ['nullable', 'string'],
            'id_tipoprograma'          => ['nullable', 'integer', 'min:1'],
            'url_video'                => ['nullable', 'string', 'max:200'],
            'estado'                   => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
