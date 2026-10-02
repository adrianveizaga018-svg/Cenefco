<?php

namespace App\Http\Requests\CampanasPublicidad;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampanaPublicidadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'programa_id'              => ['nullable', 'integer'],
            'proposito'                => ['nullable', 'in:curso,institucional,evento,reclutamiento,otro'],
            'nombre'                   => ['required', 'string', 'max:200'],
            'plataforma'               => ['required', 'in:meta_ads,google_ads,tiktok_ads,otro'],
            'objetivo'                 => ['nullable', 'string', 'max:100'],
            'fecha_inicio'             => ['required', 'date'],
            'fecha_fin'                => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado'                   => ['nullable', 'in:planificada,activa,pausada,finalizada,cancelada'],
            'leads'                    => ['nullable', 'integer', 'min:0'],
            'presupuesto_usd'          => ['nullable', 'numeric', 'min:0'],
            'presupuesto_bob'          => ['nullable', 'numeric', 'min:0'],
            'id_campana_externa'       => ['nullable', 'string', 'max:100'],
            'responsable'              => ['nullable', 'string', 'max:150'],
            'notas'                    => ['nullable', 'string'],
        ];
    }
}
