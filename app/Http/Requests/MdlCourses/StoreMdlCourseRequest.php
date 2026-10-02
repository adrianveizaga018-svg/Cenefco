<?php

namespace App\Http\Requests\MdlCourses;

use Illuminate\Foundation\Http\FormRequest;

class StoreMdlCourseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id'                      => 'required|integer',
            'num_modcurso'            => 'nullable|integer',
            'fullname'                => 'nullable|string|max:254',
            'shortname'               => 'nullable|string|max:255',
            'id_docente'              => 'nullable|integer',
            'category'                => 'nullable|integer',
            'sigla'                   => 'nullable|string|max:200',
            'paralelo'                => 'nullable|string|max:200',
            'cupo'                    => 'nullable|string|max:200',
            'grado_academico1'        => 'nullable|string|max:200',
            'grado_academico2'        => 'nullable|string|max:200',
            'observacion_imp'         => 'nullable|string',
            'imparte_fecha_inicio'    => 'nullable|date',
            'imparte_fecha_fin'       => 'nullable|date',
            'imparte_fecha_acta'      => 'nullable|date',
            'ocultar_coordinador_acta'=> 'nullable|integer',
            'id_coordinador'          => 'nullable|integer',
            'grado_coordinador1'      => 'nullable|string|max:200',
            'grado_coordinador2'      => 'nullable|string|max:200',
            'titulo_personalizado'    => 'nullable|string|max:200',
            'subtitulo_personalizado' => 'nullable|string|max:200',
            'gestion'                 => 'nullable|string|max:200',
            'per_modificar'           => 'nullable|integer',
        ];
    }
}
