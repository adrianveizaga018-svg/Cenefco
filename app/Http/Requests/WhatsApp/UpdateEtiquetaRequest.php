<?php

namespace App\Http\Requests\WhatsApp;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEtiquetaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('etiqueta') ?? $this->route('id');

        return [
            'nombre' => ["required", 'string', 'max:80', "unique:whatsapp_etiquetas,nombre,{$id}"],
            'color'  => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
