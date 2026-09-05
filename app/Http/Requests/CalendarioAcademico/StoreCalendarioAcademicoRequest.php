<?php

declare(strict_types=1);

namespace App\Http\Requests\CalendarioAcademico;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarioAcademicoRequest extends FormRequest
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
            'titulo'       => ['required', 'string', 'max:300'],
            'fecha_inicio' => ['required', 'date'],
            'descripcion'  => ['nullable', 'string'],
            'tipo'         => ['nullable', 'string', 'max:100'],
            'color'        => ['nullable', 'string', 'max:7'],
            'programa_id'  => ['nullable', 'integer'],
            'vendedor_id'    => ['nullable', 'integer', 'exists:vendedores,id'],
            'pagina'         => ['nullable', 'string', 'max:150'],
            'duracion_dias'  => ['nullable', 'integer', 'min:0'],
            'costo_inflado'  => ['nullable', 'numeric', 'min:0'],
            'descuento'      => ['nullable', 'numeric', 'min:0'],
            'precio_vip'     => ['nullable', 'numeric', 'min:0'],
            'observaciones'  => ['nullable', 'string', 'max:255'],
            'fecha_fin'    => ['nullable', 'date'],
            'todo_el_dia'  => ['nullable', 'boolean'],
            'destacado'    => ['nullable', 'boolean'],
            'publico'      => ['nullable', 'boolean'],
        ];
    }
}
