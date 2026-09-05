<?php

namespace App\Http\Requests\Cursos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCursoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_programa'          => ['required', 'string', 'max:200'],
            'slug'                     => ['nullable', 'string', 'max:300'],
            'descripcion'              => ['nullable', 'string'],
            'objetivo'                 => ['nullable', 'string'],
            'dirigido'                 => ['nullable', 'string'],
            'requisitos'               => ['nullable', 'string'],
            'inversion'                => ['nullable', 'numeric', 'min:0'],
            'costo_monto'              => ['nullable', 'numeric', 'min:0'],
            'creditaje'                => ['nullable', 'integer', 'min:0'],
            'nota'                     => ['nullable', 'string'],
            'foto'                     => ['nullable', 'string', 'max:500'],
            'titulo_documento1'        => ['nullable', 'string', 'max:200'],
            'documento1'               => ['nullable', 'string', 'max:500'],
            'imagen_banner_url'        => ['nullable', 'string', 'max:500'],
            'imagen_alt'               => ['nullable', 'string', 'max:300'],
            'url_video'                => ['nullable', 'string', 'max:500'],
            'url_whatsapp'             => ['nullable', 'string', 'max:500'],
            'url_whatsapp2'            => ['nullable', 'string', 'max:500'],
            'imagenes'                 => ['nullable', 'array'],
            'imagenes.*'               => ['nullable', 'string', 'max:500'],
            'inicio_actividades'       => ['nullable', 'date'],
            'finalizacion_actividades' => ['nullable', 'date'],
            'inicio_inscripciones'     => ['nullable', 'date'],
            'tipo_honorario'           => ['nullable', 'string', 'in:diplomado_fijo,rm_por_dia,aval_por_dia'],
            'id_tipoprograma'          => ['nullable', 'integer'],
            'categoria_web_id'         => ['nullable', 'integer'],
            'formulario_id'              => ['nullable', 'integer', 'exists:web_formulario,id'],
            'area_id'                  => ['nullable', 'integer', 'exists:web_area,id'],
            'estado_web'               => ['nullable', 'in:borrador,publicado'],
            'destacado'                => ['nullable', 'boolean'],
            'orden'                    => ['nullable', 'integer'],
            'meta_titulo'              => ['nullable', 'string', 'max:300'],
            'meta_descripcion'         => ['nullable', 'string', 'max:500'],
            'mensaje_exito'            => ['nullable', 'string'],
            'id_plan'                  => ['nullable', 'integer'],
            'id_plandoc'               => ['nullable', 'integer'],
            'id_imp'                   => ['nullable', 'integer'],
            'convenio_id'              => ['nullable', 'integer', 'exists:web_convenio,id'],
            'vendedor_id'              => ['nullable', 'integer', Rule::exists('vendedores', 'id')->whereNotNull('usuario_id')],
            'tareas_catalogo_ids'      => ['nullable', 'array'],
            'tareas_catalogo_ids.*'    => ['integer', 'exists:catalogo_tareas_academicas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'vendedor_id.exists' => 'El vendedor seleccionado no tiene un usuario del sistema vinculado.',
        ];
    }
}
