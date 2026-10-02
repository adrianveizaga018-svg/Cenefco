<?php

namespace App\Http\Requests\PlanesAcademicos;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'            => ['sometimes', 'required', 'string', 'max:200'],
            'titulo_plan'       => ['nullable', 'string', 'max:200'],
            'convenio'          => ['nullable', 'string', 'max:200'],
            'convenio_id'       => ['nullable', 'integer', 'exists:web_convenio,id'],
            'anio'              => ['nullable', 'string', 'max:200'],
            'numero_resolucion' => ['nullable', 'string', 'max:200'],
            'costo'             => ['nullable', 'string', 'max:200'],
            'nro_cuotas'        => ['nullable', 'string', 'max:200'],
            'descuento'         => ['nullable', 'string', 'max:200'],
            'costo_por_cuota'   => ['nullable', 'string', 'max:200'],
            'id_catplan'        => ['nullable', 'integer'],
            'estado'            => ['nullable', 'integer'],
        ];
    }
}
