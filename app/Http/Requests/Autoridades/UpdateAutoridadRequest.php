<?php

namespace App\Http\Requests\Autoridades;

use App\Infrastructure\Autoridades\Models\Autoridad;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAutoridadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'secretaria_id' => ['nullable', 'integer', 'exists:secretarias,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'apellido' => ['sometimes', 'required', 'string', 'max:100'],
            'cargo' => ['sometimes', 'required', 'string', 'max:100'],
            'tipo' => [
                'nullable', 'string', 'in:alcalde,subalcalde,secretario,director,jefe,otro',
                function ($attribute, $value, $fail) {
                    if ($value === 'alcalde') {
                        $id = (int) last(explode('/', trim($this->path(), '/')));
                        if (Autoridad::where('tipo', 'alcalde')->where('activo', true)->where('id', '!=', $id)->exists()) {
                            $fail('Ya existe un alcalde activo. Desactiva o elimina el actual antes de cambiar.');
                        }
                    }
                },
            ],
            'perfil_profesional' => ['nullable', 'string'],
            'email_institucional' => ['nullable', 'email', 'max:150'],
            'foto_url' => ['nullable', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
            'publicado_web' => [
                'nullable', 'boolean',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $id = (int) last(explode('/', trim($this->path(), '/')));
                        if (Autoridad::where('publicado_web', true)->where('id', '!=', $id)->exists()) {
                            $fail('Ya existe una autoridad publicada en la web. Desmarca la actual antes de publicar otra.');
                        }
                    }
                },
            ],
            'fecha_inicio_cargo' => ['nullable', 'date'],
            'fecha_fin_cargo' => ['nullable', 'date', 'after_or_equal:fecha_inicio_cargo'],
        ];
    }
}
