<?php

namespace App\Http\Requests\CampanasPublicidad;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetricaCampanaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_corte'          => ['required', 'date'],
            'alcance'              => ['nullable', 'integer', 'min:0'],
            'impresiones'          => ['nullable', 'integer', 'min:0'],
            'frecuencia'           => ['nullable', 'numeric', 'min:0'],
            'clics_enlace'         => ['nullable', 'integer', 'min:0'],
            'ctr'                  => ['nullable', 'numeric', 'min:0'],
            'cpc'                  => ['nullable', 'numeric', 'min:0'],
            'cpm'                  => ['nullable', 'numeric', 'min:0'],
            'resultados'           => ['nullable', 'integer', 'min:0'],
            'tipo_resultado'       => ['nullable', 'string', 'max:100'],
            'costo_por_resultado'  => ['nullable', 'numeric', 'min:0'],
            'gasto_periodo'        => ['nullable', 'numeric', 'min:0'],
            'fuente'               => ['nullable', 'in:manual,meta_ads_manager,google_ads,tiktok_ads'],
            'notas'                => ['nullable', 'string'],
        ];
    }
}
