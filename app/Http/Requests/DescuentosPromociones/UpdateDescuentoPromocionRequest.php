<?php

namespace App\Http\Requests\DescuentosPromociones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDescuentoPromocionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('descuento_promocion') ?? $this->route('id');

        return [
            'codigo'           => ['sometimes', 'required', 'string', 'max:50', "unique:web_descuento_promocion,codigo,{$id}"],
            'nombre'           => ['sometimes', 'required', 'string', 'max:200'],
            'valor'            => ['nullable', 'numeric', 'min:0'],
            'descripcion'      => ['nullable', 'string'],
            'tipo_descuento'   => ['nullable', 'string', 'in:porcentaje,monto_fijo'],
            'monto_minimo'     => ['nullable', 'numeric', 'min:0'],
            'usos_maximos'     => ['nullable', 'integer', 'min:1'],
            'usos_por_usuario' => ['nullable', 'integer', 'min:1'],
            'programa_id'      => ['nullable', 'integer'],
            'activo'           => ['nullable', 'boolean'],
            'fecha_inicio'     => ['nullable', 'date'],
            'fecha_fin'        => ['nullable', 'date'],
        ];
    }
}
