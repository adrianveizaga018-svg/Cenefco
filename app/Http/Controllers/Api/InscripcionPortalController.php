<?php

namespace App\Http\Controllers\Api;

use App\Domain\Formularios\Services\CampoRaizResolver;
use App\Http\Controllers\Controller;
use App\Http\Requests\InscripcionPortal\StoreInscripcionPortalRequest;
use App\Services\DocumentoEstudianteArchivador;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Portal')]
class InscripcionPortalController extends Controller
{
    public function __construct(
        private readonly DocumentoEstudianteArchivador $archivador,
    ) {}

    #[OA\Post(path: '/api/v1/public/inscripciones', summary: 'Inscripción directa desde el portal', tags: ['Portal'])]
    public function store(StoreInscripcionPortalRequest $request): JsonResponse
    {
        $idIns = DB::transaction(function () use ($request): int {
            $idImp = DB::table('t_programa')
                ->where('id_programa', $request->programa_id)
                ->value('id_imp');

            if (! $idImp) {
                abort(422, 'El programa no tiene una impartición asignada.');
            }

            $raiz = $this->resolverCamposRaiz($request);

            $documentos = $request->documentos;
            if ($documentos && ! empty($raiz['ci'])) {
                foreach ($documentos as $campo => $url) {
                    $documentos[$campo] = $this->archivador->archivar($url, $raiz['ci'], 'inscripcion', (int) $idImp, (string) $campo);
                }
            }

            $idUs = null;
            if (! empty($raiz['ci'])) {
                $idUs = DB::table('t_usuario')
                    ->where('ci', $raiz['ci'])
                    ->value('id_us');
            }

            if ($idUs) {
                $yaInscrito = DB::table('t_inscripcion')
                    ->where('id_us', $idUs)
                    ->where('id_imp', $idImp)
                    ->where('estado', 1)
                    ->exists();

                if ($yaInscrito) {
                    abort(409, 'Ya existe una inscripción activa para este usuario en el programa seleccionado.');
                }
            }

            if (! $idUs) {

                DB::statement('SELECT pg_advisory_xact_lock(1)');
                $idUs = (DB::table('t_usuario')->max('id_us') ?? 9000) + 1;

                DB::table('t_usuario')->insert([
                    'id_us'     => $idUs,
                    'nombre'    => $raiz['nombre'] ?? null,
                    'appaterno' => $raiz['apellido_paterno'] ?? null,
                    'apmaterno' => $raiz['apellido_materno'] ?? null,
                    'ci'        => $raiz['ci'] ?? null,
                    'email'     => $raiz['email'] ?? null,
                    'celular'   => $raiz['telefono'] ?? null,
                    'estado'    => 1,
                    'fecha_reg' => now()->toDateTimeString(),
                ]);
            }

            return DB::table('t_inscripcion')->insertGetId([
                'id_us_reg'   => $idUs,
                'id_us'       => $idUs,
                'id_imp'      => $idImp,
                'email'       => $raiz['email'] ?? null,
                'telefono'    => $raiz['telefono'] ?? null,
                'documentos'  => $documentos ? json_encode($documentos) : null,
                'campos_extra' => $request->campos_extra ? json_encode($request->campos_extra) : null,
                'origen'      => $request->input('origen', 'portal'),
                'canal_venta' => 'portal',
                'estado'      => 1,
                'fecha_ins'   => now()->toDateString(),
                'fecha_reg'   => now()->toDateTimeString(),
            ], 'id_ins');
        });

        // Ascender automáticamente el rol del usuario de 'participante' a 'estudiante'
        // cuando concreta una inscripción en el portal.
        $email = $request->input('email') ?? null;
        if (! $email) {
            // Intentar sacar el email de los campos del formulario
            foreach ($request->except(['programa_id', 'documentos', 'campos_extra', 'origen', 'captcha_key', 'captcha_value']) as $key => $val) {
                if (str_contains(strtolower($key), 'email') && filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $email = $val;
                    break;
                }
            }
        }

        if ($email) {
            $rolEstudiante = DB::table('roles')->where('nombre', 'Estudiante')->value('id');
            if ($rolEstudiante) {
                $usuario = DB::table('usuarios')->where('email', $email)->first(['id']);
                if ($usuario) {
                    // Solo escalar si actualmente es participante o ciudadano
                    $rolesActuales = DB::table('model_has_roles')
                        ->where('model_type', 'App\\Models\\User')
                        ->where('model_id', $usuario->id)
                        ->pluck('role_id')
                        ->toArray();

                    $rolParticipante = DB::table('roles')->whereIn('nombre', ['participante', 'Ciudadano'])->pluck('id')->toArray();

                    $esParticipante = count(array_intersect($rolesActuales, $rolParticipante)) > 0;
                    $yaEsEstudiante = in_array($rolEstudiante, $rolesActuales);

                    if ($esParticipante && ! $yaEsEstudiante) {
                        // Quitar rol anterior y asignar Estudiante
                        DB::table('model_has_roles')
                            ->where('model_type', 'App\\Models\\User')
                            ->where('model_id', $usuario->id)
                            ->delete();

                        DB::table('model_has_roles')->insert([
                            'role_id'    => $rolEstudiante,
                            'model_type' => 'App\\Models\\User',
                            'model_id'   => $usuario->id,
                        ]);
                    }
                }
            }
        }

        return response()->json(['inscripcion_id' => $idIns], 201);
    }

    private function resolverCamposRaiz(StoreInscripcionPortalRequest $request): array
    {
        $raiz   = [];
        $campos = $request->camposFormulario();

        $valores = $request->except(['programa_id', 'documentos', 'campos_extra', 'origen', 'captcha_key', 'captcha_value']);

        foreach ($campos as $campo) {
            $nombreCampo = $campo['nombre_campo'] ?? null;
            if (! $nombreCampo || ($campo['tipo'] ?? '') === 'file') continue;

            $claveRaiz = CampoRaizResolver::resolverClave($nombreCampo, $campo['rol_identidad'] ?? null);
            if (! $claveRaiz) continue;

            $valor = $valores[$nombreCampo] ?? null;

            if ($valor !== null && $valor !== '' && (! isset($raiz[$claveRaiz]) || $raiz[$claveRaiz] === null)) {
                $raiz[$claveRaiz] = $valor;
            }
        }

        foreach (CampoRaizResolver::CLAVES_RAIZ as $campo) {
            if (! isset($raiz[$campo]) && $request->filled($campo)) {
                $raiz[$campo] = $request->input($campo);
            }
        }

        return $raiz;
    }

    #[OA\Get(path: '/api/v1/public/usuarios/buscar-ci', summary: 'Buscar usuario por CI para autocompletar formulario', tags: ['Portal'])]
    public function buscarCi(): JsonResponse
    {
        $ci         = trim(request()->get('ci', ''));
        $programaId = request()->filled('programa_id') ? (int) request()->get('programa_id') : null;

        if (strlen($ci) < 4) {
            return response()->json(null);
        }

        $usuario = DB::table('t_usuario')
            ->where('ci', $ci)
            ->first(['id_us', 'nombre', 'appaterno', 'apmaterno', 'ci', 'email', 'celular']);

        if (! $usuario) {
            return response()->json(null);
        }

        $yaInscrito = false;
        if ($programaId && $usuario->id_us) {
            $idImp = DB::table('t_programa')
                ->where('id_programa', $programaId)
                ->value('id_imp');

            if ($idImp) {
                $yaInscrito = DB::table('t_inscripcion')
                    ->where('id_us', $usuario->id_us)
                    ->where('id_imp', $idImp)
                    ->where('estado', 1)
                    ->exists();
            }
        }

        return response()->json([
            'nombre'           => $usuario->nombre,
            'apellido_paterno' => $usuario->appaterno,
            'apellido_materno' => $usuario->apmaterno,
            'ci'               => $usuario->ci,
            'complemento'      => null,
            'expedido_id'      => null,
            'email'            => $usuario->email,
            'telefono'         => $usuario->celular,
            'ya_inscrito'      => $yaInscrito,
        ]);
    }
}
