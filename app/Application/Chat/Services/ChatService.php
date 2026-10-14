<?php

namespace App\Application\Chat\Services;

use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Infrastructure\Usuarios\Models\User;
use App\Services\NotificacionService;
use App\Shared\Kernel\Support\SqlCompat;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Chat interno del personal. Cada quien ve solo las conversaciones donde participa:
 * los directos son privados entre dos personas; los grupos los crea quien administra
 * el chat; el canal de avisos lo lee todo el personal y solo publica quien administra.
 */
class ChatService
{
    public const DISCO = 'local';
    public const POR_PAGINA = 50;

    public function __construct(private readonly NotificacionService $notificaciones) {}

    public function administra(User $user): bool
    {
        return $user->tienePermiso('chat.administrar');
    }

    // ── Conversaciones ──────────────────────────────────────────────────────

    public function conversaciones(User $user): array
    {
        $this->unirAAvisos($user);

        $filas = DB::table('chat_conversaciones as c')
            ->join('chat_participantes as p', fn ($j) => $j->on('p.conversacion_id', '=', 'c.id')->where('p.usuario_id', $user->id))
            ->where('c.archivada', false)
            ->orderByRaw('CASE WHEN c.ultimo_mensaje_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('c.ultimo_mensaje_at')
            ->get(['c.id', 'c.tipo', 'c.nombre', 'c.descripcion', 'c.ultimo_mensaje_at', 'p.ultimo_leido_id', 'p.rol']);

        $ids = $filas->pluck('id')->all();
        $ultimos = $this->ultimosMensajes($ids);
        $noLeidos = $this->noLeidosPorConversacion($user, $ids);
        $otros = $this->otroParticipanteDeDirectos($user, $filas->where('tipo', 'directo')->pluck('id')->all());

        return $filas->map(fn ($c) => [
            'id'             => (int) $c->id,
            'tipo'           => $c->tipo,
            'nombre'         => $c->tipo === 'directo' ? ($otros[$c->id]->nombre ?? 'Conversación') : $c->nombre,
            'descripcion'    => $c->descripcion,
            'otro_usuario_id'=> $c->tipo === 'directo' ? (int) ($otros[$c->id]->id ?? 0) : null,
            'ultimo_mensaje' => $ultimos[$c->id] ?? null,
            'no_leidos'      => (int) ($noLeidos[$c->id] ?? 0),
            'puede_publicar' => $this->puedePublicar($user, $c->tipo),
            'puede_gestionar'=> $c->tipo === 'grupo' && ($c->rol === 'admin' || $this->administra($user)),
        ])->values()->all();
    }

    /** Abre (o crea) la conversación privada con otra persona del personal. */
    public function directo(User $user, int $otroId): int
    {
        if ($otroId === (int) $user->id) {
            throw new DomainException('No puedes escribirte a ti mismo.');
        }
        if (! $this->usuariosDelChat()->where('u.id', $otroId)->exists()) {
            throw new DomainException('Esa persona no usa el chat.');
        }

        $existente = DB::table('chat_conversaciones as c')
            ->where('c.tipo', 'directo')
            ->whereExists(fn ($q) => $q->from('chat_participantes')->whereColumn('conversacion_id', 'c.id')->where('usuario_id', $user->id))
            ->whereExists(fn ($q) => $q->from('chat_participantes')->whereColumn('conversacion_id', 'c.id')->where('usuario_id', $otroId))
            ->value('c.id');
        if ($existente) {
            return (int) $existente;
        }

        return DB::transaction(function () use ($user, $otroId) {
            $id = DB::table('chat_conversaciones')->insertGetId(['tipo' => 'directo', 'creado_por' => $user->id, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('chat_participantes')->insert([
                ['conversacion_id' => $id, 'usuario_id' => $user->id, 'rol' => 'miembro', 'unido_at' => now()],
                ['conversacion_id' => $id, 'usuario_id' => $otroId, 'rol' => 'miembro', 'unido_at' => now()],
            ]);

            return $id;
        });
    }

    public function crearGrupo(User $user, string $nombre, ?string $descripcion, array $miembros): int
    {
        if (! $this->administra($user)) {
            throw new DomainException('Solo quien administra el chat puede crear grupos.');
        }
        $validos = $this->usuariosDelChat()->whereIn('u.id', $miembros)->pluck('u.id')->map(fn ($v) => (int) $v)->all();

        return DB::transaction(function () use ($user, $nombre, $descripcion, $validos) {
            $id = DB::table('chat_conversaciones')->insertGetId([
                'tipo' => 'grupo', 'nombre' => trim($nombre), 'descripcion' => $descripcion, 'creado_por' => $user->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $filas = collect(array_unique(array_merge([(int) $user->id], $validos)))
                ->map(fn ($uid) => ['conversacion_id' => $id, 'usuario_id' => $uid, 'rol' => $uid === (int) $user->id ? 'admin' : 'miembro', 'unido_at' => now()]);
            DB::table('chat_participantes')->insert($filas->all());

            return $id;
        });
    }

    public function actualizarGrupo(User $user, int $id, array $datos): void
    {
        $grupo = DB::table('chat_conversaciones')->where('id', $id)->where('tipo', 'grupo')->first();
        if (! $grupo) {
            throw new DomainException('Grupo no encontrado.');
        }
        $esAdminDelGrupo = DB::table('chat_participantes')->where('conversacion_id', $id)->where('usuario_id', $user->id)->where('rol', 'admin')->exists();
        if (! $esAdminDelGrupo && ! $this->administra($user)) {
            throw new DomainException('Solo quien administra el grupo puede cambiarlo.');
        }

        DB::transaction(function () use ($id, $datos) {
            $cambios = array_filter(['nombre' => $datos['nombre'] ?? null, 'descripcion' => $datos['descripcion'] ?? null], fn ($v) => $v !== null);
            if (array_key_exists('archivada', $datos)) {
                $cambios['archivada'] = (bool) $datos['archivada'];
            }
            if ($cambios) {
                DB::table('chat_conversaciones')->where('id', $id)->update($cambios + ['updated_at' => now()]);
            }
            $agregar = $this->usuariosDelChat()->whereIn('u.id', $datos['agregar'] ?? [])->pluck('u.id');
            foreach ($agregar as $uid) {
                DB::table('chat_participantes')->insertOrIgnore(['conversacion_id' => $id, 'usuario_id' => $uid, 'rol' => 'miembro', 'unido_at' => now()]);
            }
            if (! empty($datos['quitar'])) {
                DB::table('chat_participantes')->where('conversacion_id', $id)->whereIn('usuario_id', $datos['quitar'])->where('rol', '!=', 'admin')->delete();
            }
        });
    }

    // ── Mensajes ────────────────────────────────────────────────────────────

    /** Página de mensajes: los más recientes, o anteriores a `antesDe`, o nuevos después de `despuesDe`. */
    public function mensajes(User $user, int $idConv, ?int $antesDe = null, ?int $despuesDe = null): array
    {
        $conv = $this->conversacionPermitida($user, $idConv);

        $q = DB::table('chat_mensajes as m')
            ->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
            ->where('m.conversacion_id', $idConv)
            ->select(['m.*', DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as autor')]);

        if ($despuesDe) {
            $filas = $q->where('m.id', '>', $despuesDe)->orderBy('m.id')->limit(200)->get();
        } else {
            $filas = $q->when($antesDe, fn ($q) => $q->where('m.id', '<', $antesDe))
                ->orderByDesc('m.id')->limit(self::POR_PAGINA)->get()->reverse()->values();
        }

        $respuestas = DB::table('chat_mensajes as m')->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
            ->whereIn('m.id', $filas->pluck('responde_a_id')->filter()->unique())
            ->get(['m.id', 'm.cuerpo', 'm.adjunto_nombre', 'm.eliminado_at', DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as autor')])
            ->keyBy('id');
        $menciones = DB::table('chat_menciones')->whereIn('mensaje_id', $filas->pluck('id'))->get()->groupBy('mensaje_id');

        $mensajes = $filas->map(fn ($m) => $this->formatear($m, $user, $respuestas, $menciones))->values()->all();

        return [
            'conversacion' => [
                'id' => (int) $conv->id, 'tipo' => $conv->tipo, 'nombre' => $conv->nombre, 'descripcion' => $conv->descripcion,
                'puede_publicar' => $this->puedePublicar($user, $conv->tipo),
            ],
            'mensajes'      => $mensajes,
            'hay_mas'       => ! $despuesDe && count($mensajes) === self::POR_PAGINA,
            'participantes' => $this->participantes($idConv),
        ];
    }

    public function enviar(User $user, int $idConv, ?string $cuerpo, ?UploadedFile $archivo, ?int $respondeA, array $menciones): array
    {
        $conv = $this->conversacionPermitida($user, $idConv);
        if (! $this->puedePublicar($user, $conv->tipo)) {
            throw new DomainException('En el canal de avisos solo publica Dirección.');
        }
        $cuerpo = $cuerpo !== null ? trim($cuerpo) : null;
        if (($cuerpo === null || $cuerpo === '') && ! $archivo) {
            throw new DomainException('Escribe un mensaje o adjunta un archivo.');
        }
        if ($respondeA && ! DB::table('chat_mensajes')->where('id', $respondeA)->where('conversacion_id', $idConv)->exists()) {
            $respondeA = null;
        }

        $adjunto = $archivo ? [
            'adjunto_ruta'   => $archivo->store("chat/{$idConv}", self::DISCO),
            'adjunto_nombre' => Str::limit($archivo->getClientOriginalName(), 250, ''),
            'adjunto_tipo'   => $archivo->getMimeType(),
            'adjunto_peso'   => $archivo->getSize(),
        ] : [];

        $idMensaje = DB::transaction(function () use ($user, $idConv, $cuerpo, $respondeA, $adjunto, $menciones) {
            $id = DB::table('chat_mensajes')->insertGetId([
                'conversacion_id' => $idConv, 'usuario_id' => $user->id, 'cuerpo' => $cuerpo ?: null,
                'responde_a_id' => $respondeA, 'created_at' => now(),
            ] + $adjunto);
            DB::table('chat_conversaciones')->where('id', $idConv)->update(['ultimo_mensaje_at' => now(), 'updated_at' => now()]);
            DB::table('chat_participantes')->where('conversacion_id', $idConv)->where('usuario_id', $user->id)->update(['ultimo_leido_id' => $id]);

            $mencionados = DB::table('chat_participantes')->where('conversacion_id', $idConv)
                ->whereIn('usuario_id', $menciones)->where('usuario_id', '!=', $user->id)->pluck('usuario_id');
            foreach ($mencionados as $uid) {
                DB::table('chat_menciones')->insert(['mensaje_id' => $id, 'usuario_id' => $uid]);
            }

            return $id;
        });

        $this->avisar($user, $conv, $idMensaje, (string) $cuerpo);

        return $this->mensajes($user, $idConv, despuesDe: $idMensaje - 1)['mensajes'][0];
    }

    public function marcarLeido(User $user, int $idConv, int $ultimoId): void
    {
        $this->conversacionPermitida($user, $idConv);
        DB::table('chat_participantes')->where('conversacion_id', $idConv)->where('usuario_id', $user->id)
            ->where('ultimo_leido_id', '<', $ultimoId)->update(['ultimo_leido_id' => $ultimoId]);
    }

    /** Quien lo escribió (o quien administra el chat) puede borrar un mensaje; queda "Mensaje eliminado". */
    public function eliminar(User $user, int $idMensaje): void
    {
        $m = DB::table('chat_mensajes')->where('id', $idMensaje)->first();
        if (! $m || $m->eliminado_at) {
            throw new DomainException('Mensaje no encontrado.');
        }
        if ((int) $m->usuario_id !== (int) $user->id && ! $this->administra($user)) {
            throw new DomainException('Solo puedes eliminar tus propios mensajes.');
        }
        $this->conversacionPermitida($user, (int) $m->conversacion_id);

        DB::table('chat_mensajes')->where('id', $idMensaje)->update(['eliminado_at' => now(), 'cuerpo' => null]);
        if ($m->adjunto_ruta) {
            Storage::disk(self::DISCO)->delete($m->adjunto_ruta);
            DB::table('chat_mensajes')->where('id', $idMensaje)->update(['adjunto_ruta' => null, 'adjunto_nombre' => null, 'adjunto_tipo' => null, 'adjunto_peso' => null]);
        }
    }

    /** @return array{ruta: string, nombre: string, tipo: ?string} */
    public function adjunto(User $user, int $idMensaje): array
    {
        $m = DB::table('chat_mensajes')->where('id', $idMensaje)->whereNull('eliminado_at')->first();
        if (! $m || ! $m->adjunto_ruta) {
            throw new DomainException('Archivo no encontrado.');
        }
        $this->conversacionPermitida($user, (int) $m->conversacion_id);

        return ['ruta' => $m->adjunto_ruta, 'nombre' => $m->adjunto_nombre, 'tipo' => $m->adjunto_tipo];
    }

    public function totalNoLeidos(User $user): int
    {
        $this->unirAAvisos($user);
        $ids = DB::table('chat_participantes as p')->join('chat_conversaciones as c', 'c.id', '=', 'p.conversacion_id')
            ->where('p.usuario_id', $user->id)->where('c.archivada', false)->pluck('c.id')->all();

        return (int) array_sum($this->noLeidosPorConversacion($user, $ids));
    }

    public function buscar(User $user, string $texto): array
    {
        $texto = trim($texto);
        if (mb_strlen($texto) < 2) {
            return [];
        }

        return DB::table('chat_mensajes as m')
            ->join('chat_participantes as p', fn ($j) => $j->on('p.conversacion_id', '=', 'm.conversacion_id')->where('p.usuario_id', $user->id))
            ->join('chat_conversaciones as c', 'c.id', '=', 'm.conversacion_id')
            ->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
            ->whereNull('m.eliminado_at')
            ->where(fn ($q) => $q->whereRaw('LOWER(m.cuerpo) LIKE ?', ['%'.mb_strtolower($texto).'%'])->orWhereRaw('LOWER(m.adjunto_nombre) LIKE ?', ['%'.mb_strtolower($texto).'%']))
            ->orderByDesc('m.id')->limit(50)
            ->get(['m.id', 'm.conversacion_id', 'm.cuerpo', 'm.adjunto_nombre', 'm.created_at', 'c.tipo', 'c.nombre as conversacion',
                DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as autor')])
            ->all();
    }

    /** Personal que usa el chat (para iniciar un directo o armar un grupo). */
    public function directorio(User $user, ?string $texto = null): array
    {
        return $this->usuariosDelChat()
            ->where('u.id', '!=', $user->id)
            ->when($texto, fn ($q) => $q->where(fn ($w) => $w->whereRaw('LOWER(u.nombre) LIKE ?', ['%'.mb_strtolower($texto).'%'])
                ->orWhereRaw('LOWER(u.apellido) LIKE ?', ['%'.mb_strtolower($texto).'%'])))
            ->orderBy('nombre')->limit(200) // por el alias: con DISTINCT, PostgreSQL solo ordena por columnas seleccionadas
            ->get(['u.id', DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as nombre'), 'u.avatar_url'])
            ->unique('id')->values()->all();
    }

    // ── Internos ────────────────────────────────────────────────────────────

    private function puedePublicar(User $user, string $tipo): bool
    {
        return $tipo !== 'anuncios' || $this->administra($user);
    }

    private function conversacionPermitida(User $user, int $idConv): object
    {
        $conv = DB::table('chat_conversaciones')->where('id', $idConv)->first();
        if ($conv && $conv->tipo === 'anuncios') {
            $this->unirAAvisos($user);
        }
        $participa = $conv && DB::table('chat_participantes')->where('conversacion_id', $idConv)->where('usuario_id', $user->id)->exists();
        if (! $participa) {
            throw new DomainException('Conversación no encontrada.');
        }

        return $conv;
    }

    /** El canal de avisos incluye a todo el personal: se suma al abrir el chat. */
    private function unirAAvisos(User $user): void
    {
        foreach (DB::table('chat_conversaciones')->where('tipo', 'anuncios')->where('archivada', false)->pluck('id') as $id) {
            DB::table('chat_participantes')->insertOrIgnore([
                'conversacion_id' => $id, 'usuario_id' => $user->id,
                'rol' => $this->administra($user) ? 'admin' : 'miembro', 'unido_at' => now(),
            ]);
        }
    }

    /** Usuarios activos cuyo rol incluye chat.usar (o todos los permisos). */
    private function usuariosDelChat()
    {
        return DB::table('usuarios as u')
            ->join('usuarios_roles as ur', 'ur.usuario_id', '=', 'u.id')
            ->join('roles_permisos as rp', 'rp.rol_id', '=', 'ur.rol_id')
            ->join('permisos as pe', 'pe.id', '=', 'rp.permiso_id')
            ->whereIn('pe.codigo', ['chat.usar', '*'])
            ->where('u.activo', true)
            ->whereNull('u.deleted_at')
            ->distinct();
    }

    private function participantes(int $idConv): array
    {
        return DB::table('chat_participantes as p')->join('usuarios as u', 'u.id', '=', 'p.usuario_id')
            ->where('p.conversacion_id', $idConv)
            ->orderBy('u.nombre')
            ->get(['u.id', DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as nombre'), 'p.rol', 'p.ultimo_leido_id'])
            ->map(fn ($p) => ['id' => (int) $p->id, 'nombre' => $p->nombre, 'rol' => $p->rol, 'ultimo_leido_id' => (int) $p->ultimo_leido_id])
            ->all();
    }

    private function formatear(object $m, User $user, Collection $respuestas, Collection $menciones): array
    {
        $eliminado = (bool) $m->eliminado_at;
        $resp = $m->responde_a_id ? $respuestas->get($m->responde_a_id) : null;

        return [
            'id'         => (int) $m->id,
            'usuario_id' => (int) $m->usuario_id,
            'autor'      => trim((string) $m->autor) ?: 'Usuario',
            'es_mio'     => (int) $m->usuario_id === (int) $user->id,
            'cuerpo'     => $eliminado ? null : $m->cuerpo,
            'eliminado'  => $eliminado,
            'adjunto'    => ! $eliminado && $m->adjunto_ruta ? [
                'nombre' => $m->adjunto_nombre, 'tipo' => $m->adjunto_tipo, 'peso' => (int) $m->adjunto_peso,
                'es_imagen' => str_starts_with((string) $m->adjunto_tipo, 'image/'),
            ] : null,
            'responde_a' => $resp ? [
                'id' => (int) $resp->id, 'autor' => trim((string) $resp->autor),
                'cuerpo' => $resp->eliminado_at ? 'Mensaje eliminado' : Str::limit((string) ($resp->cuerpo ?: $resp->adjunto_nombre), 120),
            ] : null,
            'menciones'  => ($menciones->get($m->id) ?? collect())->pluck('usuario_id')->map(fn ($v) => (int) $v)->all(),
            'created_at' => $m->created_at,
        ];
    }

    private function ultimosMensajes(array $ids): array
    {
        if (! $ids) {
            return [];
        }
        $ultimoIds = DB::table('chat_mensajes')->whereIn('conversacion_id', $ids)->groupBy('conversacion_id')->selectRaw('MAX(id) as id')->pluck('id');

        return DB::table('chat_mensajes as m')->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
            ->whereIn('m.id', $ultimoIds)
            ->get(['m.conversacion_id', 'm.cuerpo', 'm.adjunto_nombre', 'm.eliminado_at', 'm.created_at', 'u.nombre as autor'])
            ->mapWithKeys(fn ($m) => [$m->conversacion_id => [
                'texto' => $m->eliminado_at ? 'Mensaje eliminado' : Str::limit((string) ($m->cuerpo ?: '📎 '.$m->adjunto_nombre), 80),
                'autor' => $m->autor,
                'fecha' => $m->created_at,
            ]])->all();
    }

    private function noLeidosPorConversacion(User $user, array $ids): array
    {
        if (! $ids) {
            return [];
        }

        return DB::table('chat_mensajes as m')
            ->join('chat_participantes as p', fn ($j) => $j->on('p.conversacion_id', '=', 'm.conversacion_id')->where('p.usuario_id', $user->id))
            ->whereIn('m.conversacion_id', $ids)
            ->whereColumn('m.id', '>', 'p.ultimo_leido_id')
            ->where('m.usuario_id', '!=', $user->id)
            ->whereNull('m.eliminado_at')
            ->groupBy('m.conversacion_id')
            ->selectRaw('m.conversacion_id, COUNT(*) as n')
            ->pluck('n', 'conversacion_id')->all();
    }

    private function otroParticipanteDeDirectos(User $user, array $ids): array
    {
        if (! $ids) {
            return [];
        }

        return DB::table('chat_participantes as p')->join('usuarios as u', 'u.id', '=', 'p.usuario_id')
            ->whereIn('p.conversacion_id', $ids)->where('p.usuario_id', '!=', $user->id)
            ->get(['p.conversacion_id', 'u.id', DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as nombre')])
            ->keyBy('conversacion_id')->all();
    }

    /** Menciones y avisos llegan a la campanita; el resto se ve en el contador del chat. */
    private function avisar(User $autor, object $conv, int $idMensaje, string $cuerpo): void
    {
        try {
            $nombreAutor = trim($autor->nombre.' '.$autor->apellido);
            $donde = $conv->tipo === 'directo' ? 'un mensaje directo' : '#'.$conv->nombre;
            $texto = Str::limit($cuerpo ?: 'Adjuntó un archivo', 140);

            foreach (DB::table('chat_menciones')->where('mensaje_id', $idMensaje)->pluck('usuario_id') as $uid) {
                $this->notificaciones->enviar(
                    destinatario: DestinatarioEnum::USUARIO, tipo: 'chat_mencion',
                    titulo: "{$nombreAutor} te mencionó en {$donde}", mensaje: $texto,
                    prioridad: PrioridadEnum::ALTA, usuarioId: (int) $uid,
                    urlAccion: "/chat?c={$conv->id}", icono: 'lucideAtSign', color: '#0b4258',
                );
            }

            if ($conv->tipo === 'anuncios') {
                $this->notificaciones->enviarAPermiso(
                    permiso: 'chat.usar', tipo: 'chat_aviso',
                    titulo: "Aviso de Dirección: {$nombreAutor}", mensaje: $texto,
                    prioridad: PrioridadEnum::ALTA, urlAccion: "/chat?c={$conv->id}", icono: 'lucideMegaphone', color: '#e8851a',
                );
            }
        } catch (\Throwable $e) {
            report($e); // un aviso que falla nunca debe impedir enviar el mensaje
        }
    }
}
