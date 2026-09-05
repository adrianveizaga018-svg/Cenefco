<?php

declare(strict_types=1);

namespace App\Http\Requests\CalendarioAcademico;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalendarioAcademicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('vendedor_id') && (int) $this->input('vendedor_id') === 0) {
            $this->merge(['vendedor_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'titulo'       => ['sometimes', 'nullable', 'string', 'max:300'],
            'descripcion'  => ['sometimes', 'nullable', 'string'],
            'tipo'         => ['sometimes', 'nullable', 'string', 'max:100'],
            'color'        => ['sometimes', 'nullable', 'string', 'max:7'],
            'programa_id'  => ['sometimes', 'nullable', 'integer'],
            'vendedor_id'    => ['sometimes', 'nullable', 'integer', 'exists:vendedores,id'],
            'pagina'         => ['sometimes', 'nullable', 'string', 'max:150'],
            'duracion_dias'  => ['sometimes', 'nullable', 'integer', 'min:0'],
            'costo_inflado'  => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'descuento'      => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'precio_vip'     => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'observaciones'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'fecha_inicio' => ['sometimes', 'nullable', 'date'],
            'fecha_fin'    => ['sometimes', 'nullable', 'date'],
            'todo_el_dia'  => ['sometimes', 'nullable', 'boolean'],
            'destacado'    => ['sometimes', 'nullable', 'boolean'],
            'publico'      => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
