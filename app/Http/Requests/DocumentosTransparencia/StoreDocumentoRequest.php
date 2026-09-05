<?php

namespace App\Http\Requests\DocumentosTransparencia;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRequest extends FormRequest
{
    private const CATEGORIA_MAP = [
        'presupuesto' => 5,
        'contrato' => 2,
        'resolucion' => 10,
        'ordenanza' => 8,
        'informe' => 3,
        'declaracion_bienes' => 1,
        'plan_anual' => 9,
        'rendicion_cuentas' => 12,
        'otro' => 3,
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('categoria') && ! $this->has('tipo_documento_id')) {
            $this->merge([
                'tipo_documento_id' => self::CATEGORIA_MAP[$this->categoria] ?? 3,
            ]);
        }
        if ($this->has('anio') && ! $this->has('gestion')) {
            $this->merge(['gestion' => $this->anio]);
        }
        if ($this->has('publicado') && ! $this->has('activo')) {
            $this->merge(['activo' => $this->publicado]);
        }
        if ($this->has('archivo_nombre') && ! $this->has('archivo_url')) {
        }
    }

    public function rules(): array
    {
        return [
            'tipo_documento_id' => ['required', 'exists:tipos_documento_transparencia,id'],
            'secretaria_id' => ['nullable', 'exists:secretarias,id'],
            'titulo' => ['required', 'string', 'max:300'],
            'descripcion' => ['nullable', 'string'],
            'archivo_url' => ['nullable', 'string', 'max:500'],
            'gestion' => ['required', 'integer'],
            'fecha_publicacion' => ['nullable', 'date'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
