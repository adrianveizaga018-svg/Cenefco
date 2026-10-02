<?php

namespace App\Http\Requests\InscripcionPortal;

use App\Domain\Formularios\Services\CampoRaizResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreInscripcionPortalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $base = [
            'programa_id'      => ['required', 'integer', 'exists:t_programa,id_programa'],
            'ci'               => ['nullable', 'string', 'max:30'],
            'nombre'           => ['nullable', 'string', 'max:200'],
            'apellido_paterno' => ['nullable', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'email'            => ['nullable', 'email', 'max:200'],
            'telefono'         => ['nullable', 'string', 'max:30'],
            'documentos'       => ['nullable', 'array'],
            'campos_extra'     => ['nullable', 'array'],
            'origen'           => ['nullable', 'string', 'max:30'],
            'captcha_key'      => ['required', 'string'],
            'captcha_value'    => ['required', 'captcha_api:' . $this->input('captcha_key') . ',inscripcion'],
        ];

        foreach ($this->camposFormulario() as $campo) {
            $tipo   = $campo['tipo'] ?? 'text';
            $nombre = $campo['nombre_campo'] ?? null;
            if (! $nombre) continue;

            if ($tipo === 'file') {

                if (! empty($campo['requerido'])) {
                    $base["documentos.{$nombre}"] = ['required', 'string'];
                }
                continue;
            }

            $claveRaiz = CampoRaizResolver::resolverClave($nombre, $campo['rol_identidad'] ?? null);
            $clave     = $claveRaiz ? $claveRaiz : "campos_extra.{$nombre}";

            $reglas = $this->reglasParaTipo($tipo, $campo['validacion'] ?? null, $campo['opciones'] ?? []);

            if (! empty($campo['requerido'])) {
                array_unshift($reglas, 'required');
            } else {
                array_unshift($reglas, 'nullable');
            }

            $base[$clave] = $reglas;
        }

        return $base;
    }

    private function reglasParaTipo(string $tipo, ?array $validacion, array $opciones): array
    {
        $reglas = match ($tipo) {
            'email'    => ['email', 'max:200'],
            'number'   => ['numeric'],
            'date'     => ['date'],
            'tel'      => ['string', 'max:30'],
            'select'   => !empty($opciones) ? [Rule::in($opciones)] : ['string'],
            'checkbox' => ['boolean'],
            'textarea' => ['string', 'max:5000'],
            default    => ['string', 'max:500'],
        };

        if (! $validacion) {
            return $reglas;
        }

        if (in_array($tipo, ['number', 'date'], true)) {
            if (isset($validacion['min']) && $validacion['min'] !== null) {
                $reglas[] = "min:{$validacion['min']}";
            }
            if (isset($validacion['max']) && $validacion['max'] !== null) {
                $reglas[] = "max:{$validacion['max']}";
            }
        }

        if (in_array($tipo, ['text', 'textarea', 'tel'], true)) {
            if (isset($validacion['min_length']) && $validacion['min_length'] !== null) {
                $reglas[] = "min:{$validacion['min_length']}";
            }
            if (isset($validacion['max_length']) && $validacion['max_length'] !== null) {

                $reglas = array_filter($reglas, fn ($r) => !str_starts_with($r, 'max:'));
                $reglas[] = "max:{$validacion['max_length']}";
            }
            if (! empty($validacion['patron'])) {

                $patron = $validacion['patron'];
                $reglas[] = function (string $attribute, mixed $value, \Closure $fail) use ($patron): void {
                    if ($value === null || $value === '') return;
                    if (@preg_match('#'.str_replace('#', '\#', $patron).'#u', (string) $value) !== 1) {
                        $fail('El formato de :attribute no es válido.');
                    }
                };
            }
        }

        return array_values($reglas);
    }

    public function messages(): array
    {
        return [
            'captcha_key.required'   => 'El captcha es requerido.',
            'captcha_value.required' => 'Ingrese el código de verificación.',
            'captcha_value.captcha_api' => 'El código de verificación es incorrecto.',
        ];
    }

    public function attributes(): array
    {
        $attrs = [];
        foreach ($this->camposFormulario() as $campo) {
            $nombre = $campo['nombre_campo'] ?? null;
            if (! $nombre || empty($campo['etiqueta'])) continue;

            $claveRaiz = CampoRaizResolver::resolverClave($nombre, $campo['rol_identidad'] ?? null);
            $clave = $claveRaiz ?? "campos_extra.{$nombre}";
            $attrs[$clave] = $campo['etiqueta'];

            if (($campo['tipo'] ?? '') === 'file') {
                $attrs["documentos.{$nombre}"] = $campo['etiqueta'];
            }
        }
        return $attrs;
    }

    public function camposFormulario(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $programaId = (int) $this->input('programa_id');
        if (! $programaId) return $cache = [];

        $formularioId = DB::table('t_programa')
            ->where('id_programa', $programaId)
            ->value('formulario_id');

        if (! $formularioId) return $cache = [];

        $camposJson = DB::table('web_formulario')
            ->where('id', $formularioId)
            ->value('campos');

        $cache = is_string($camposJson) ? (json_decode($camposJson, true) ?? []) : [];
        return $cache;
    }
}
