<?php

namespace App\Http\Requests\CertPlantillas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertPlantillaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'         => ['sometimes', 'required', 'string', 'max:300'],
            'tipo'           => ['nullable', 'string', 'max:50'],
            'imagen_url'     => ['sometimes', 'required', 'string', 'max:500'],
            'ancho_px'       => ['nullable', 'integer'],
            'alto_px'        => ['nullable', 'integer'],
            'orientacion'    => ['nullable', 'in:horizontal,vertical'],
            'formato_salida' => ['nullable', 'in:jpg,png,pdf'],
            'calidad_jpg'    => ['nullable', 'integer', 'min:1', 'max:100'],
            'fuente_default' => ['nullable', 'string', 'max:100'],
            'color_default'  => ['nullable', 'string', 'max:7'],
            'estado'         => ['nullable', 'string', 'max:50'],
            'notas'          => ['nullable', 'string'],
        ];
    }
}
