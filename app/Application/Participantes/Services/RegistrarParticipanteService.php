<?php

namespace App\Application\Participantes\Services;

use App\Infrastructure\ListaAprobados\Models\ListaAprobados;
use App\Infrastructure\UsuariosAcademicos\Models\UsuarioAcademico;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrarParticipanteService
{
    public function registrar(int $imparteId, array $datos): ListaAprobados
    {
        return DB::transaction(function () use ($imparteId, $datos): ListaAprobados {
            $ci = trim((string) ($datos['ci'] ?? ''));

            if ($ci === '') {
                $nombreCompleto = trim(implode(' ', array_filter([
                    trim((string) ($datos['appaterno'] ?? '')),
                    trim((string) ($datos['apmaterno'] ?? '')),
                    trim($datos['nombre']),
                ])));

                $this->verificarDuplicadoPorNombre($imparteId, $nombreCompleto);
            }

            $usuario = $ci !== '' ? UsuarioAcademico::where('ci', $ci)->orderBy('id_us')->first() : null;

            if (! $usuario) {

                DB::statement('LOCK TABLE t_usuario IN SHARE ROW EXCLUSIVE MODE');

                $nuevoId = (int) DB::selectOne(
                    'SELECT COALESCE(MAX(id_us), 0) + 1 AS next_id FROM t_usuario'
                )->next_id;

                $appaterno = trim($datos['appaterno'] ?? '');
                $apmaterno = trim($datos['apmaterno'] ?? '');

                $usuario = UsuarioAcademico::create([
                    'id_us'     => $nuevoId,
                    'id_us_reg' => 0,
                    'nombre'    => trim($datos['nombre']),
                    'appaterno' => $appaterno !== '' ? $appaterno : null,
                    'apmaterno' => $apmaterno !== '' ? $apmaterno : null,
                    'ci'        => $ci !== '' ? $ci : null,
                    'email'     => $datos['email'] ?? null,
                    'estado'    => 1,
                    'fecha_reg' => now()->toDateTimeString(),
                ]);
            }

            $usuarioId = (int) $usuario->id_us;

            $existe = ListaAprobados::where('imparte_id', $imparteId)
                ->where('usuario_id', $usuarioId)
                ->exists();

            if ($existe) {
                $nombreCompleto = trim(implode(' ', array_filter([
                    $usuario->appaterno,
                    $usuario->apmaterno,
                    $usuario->nombre,
                ])));
                throw ValidationException::withMessages([
                    'ci' => ["El participante \"{$nombreCompleto}\" (CI: {$ci}) ya está registrado en este curso."],
                ]);
            }

            return ListaAprobados::create([
                'imparte_id'         => $imparteId,
                'usuario_id'         => $usuarioId,
                'condicion'          => $datos['condicion'] ?? 'aprobado',
                'nota_final'         => isset($datos['nota_final']) && $datos['nota_final'] !== '' ? (float) $datos['nota_final'] : null,
                'observacion'        => $datos['observacion'] ?? null,
                'comprobante_url'    => $datos['comprobante_url'] ?? null,
                'ajuste_manual'      => true,
                'estado_certificado' => 'pendiente',
                'id_us_reg'          => 0,
                'created_at'         => now()->toDateTimeString(),
            ]);
        });
    }

    public function nombreCompleto(UsuarioAcademico $usuario): string
    {
        return trim(implode(' ', array_filter([
            $usuario->appaterno,
            $usuario->apmaterno,
            $usuario->nombre,
        ])));
    }

    private function verificarDuplicadoPorNombre(int $imparteId, string $nombreCompleto): void
    {
        $normalizado = $this->normalizarNombre($nombreCompleto);

        $yaRegistrado = DB::table('t_lista_aprobados as la')
            ->join('t_usuario as us', 'us.id_us', '=', 'la.usuario_id')
            ->where('la.imparte_id', $imparteId)
            ->get(['us.nombre', 'us.appaterno', 'us.apmaterno'])
            ->contains(function ($us) use ($normalizado) {
                $completo = trim(implode(' ', array_filter([$us->appaterno, $us->apmaterno, $us->nombre])));

                return $this->normalizarNombre($completo) === $normalizado;
            });

        if ($yaRegistrado) {
            throw ValidationException::withMessages([
                'nombre' => ["\"{$nombreCompleto}\" ya está registrado en este curso."],
            ]);
        }
    }

    private function normalizarNombre(string $texto): string
    {
        return mb_strtoupper(trim(preg_replace('/\s+/', ' ', $texto) ?? $texto));
    }

    public static function reglas(bool $ciOpcional = false): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:120'],
            'appaterno'   => ['nullable', 'string', 'max:100'],
            'apmaterno'   => ['nullable', 'string', 'max:100'],
            'ci'          => [$ciOpcional ? 'nullable' : 'required', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:150'],
            'condicion'   => ['nullable', 'string', 'in:aprobado,reprobado,retirado'],
            'nota_final'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ];
    }
}
