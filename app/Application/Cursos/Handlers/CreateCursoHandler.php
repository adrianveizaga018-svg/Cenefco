<?php

namespace App\Application\Cursos\Handlers;

use App\Application\Cursos\Commands\CreateCursoCommand;
use App\Application\Cursos\DTOs\CursoDTO;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;
use App\Domain\Honorarios\Contracts\ConfigHonorarioRepositoryInterface;
use App\Infrastructure\Imparticiones\Models\Imparte;
use App\Infrastructure\Materias\Models\Materia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCursoHandler
{
    public function __construct(
        private readonly CursoRepositoryInterface           $repository,
        private readonly ConfigHonorarioRepositoryInterface $configHonorarioRepository,
        private readonly \App\Domain\TareasAcademicas\Repositories\TareaAcademicaRepositoryInterface $tareaAcademicaRepository,
    ) {}

    public function handle(CreateCursoCommand $c): CursoDTO
    {
        return DB::transaction(fn () => $this->crear($c));
    }

    private function crear(CreateCursoCommand $c): CursoDTO
    {
        $idImp = $c->id_imp ?? $this->crearImparticionPropia($c->nombre_programa);

        $dto = $this->repository->create([
            'nombre_programa'          => $c->nombre_programa,
            'slug'                     => $c->slug ?: Str::slug($c->nombre_programa),
            'descripcion'              => $c->descripcion,
            'objetivo'                 => $c->objetivo,
            'dirigido'                 => $c->dirigido,
            'requisitos'               => $c->requisitos,
            'inversion'                => $c->inversion,
            'costo_monto'              => $c->costo_monto,
            'creditaje'                => $c->creditaje,
            'nota'                     => $c->nota,
            'foto'                     => $c->foto,
            'titulo_documento1'        => $c->titulo_documento1,
            'documento1'               => $c->documento1,
            'imagen_banner_url'        => $c->imagen_banner_url,
            'imagen_alt'               => $c->imagen_alt,
            'url_video'                => $c->url_video,
            'url_whatsapp'             => $c->url_whatsapp,
            'url_whatsapp2'            => $c->url_whatsapp2,
            'imagenes'                 => $c->imagenes ?? [],
            'inicio_actividades'       => $c->inicio_actividades,
            'finalizacion_actividades' => $c->finalizacion_actividades,
            'inicio_inscripciones'     => $c->inicio_inscripciones,
            'id_tipoprograma'          => $c->id_tipoprograma,
            'categoria_web_id'         => $c->categoria_web_id,
            'formulario_id'               => $c->formulario_id,
            'area_id'                  => $c->area_id,
            'estado_web'               => $c->estado_web,
            'destacado'                => $c->destacado,
            'orden'                    => $c->orden,
            'meta_titulo'              => $c->meta_titulo,
            'meta_descripcion'         => $c->meta_descripcion,
            'mensaje_exito'            => $c->mensaje_exito,
            'id_plan'                  => $c->id_plan,
            'id_plandoc'               => $c->id_plandoc,
            'id_imp'                   => $idImp,
            'convenio_id'              => $c->convenio_id,
            'vendedor_id'              => $c->vendedor_id,
        ]);

        if ($c->tipo_honorario !== null) {
            $this->configHonorarioRepository->upsert($dto->id_programa, [
                'tipo_honorario' => $c->tipo_honorario,
                'monto_fijo'     => null,
                'monto_por_dia'  => null,
            ]);

            $dto = $this->repository->findById($dto->id_programa);
        }

        if (!empty($c->tareas_catalogo_ids)) {
            $catalogoTareas = \App\Infrastructure\TareasAcademicas\Models\CatalogoTareaAcademica::whereIn('id', $c->tareas_catalogo_ids)->get();
            foreach ($catalogoTareas as $catTarea) {
                $this->tareaAcademicaRepository->create([
                    'programa_id'      => $dto->id_programa,
                    'titulo'           => $catTarea->titulo,
                    'requiere_archivo' => $catTarea->requiere_archivo,
                    'estado'           => 'pendiente',
                    'catalogo_id'      => $catTarea->id,
                ]);
            }
        }

        return $dto;
    }

    private function crearImparticionPropia(string $nombrePrograma): int
    {
        return DB::transaction(function () use ($nombrePrograma) {
            $ultimaMateria = Materia::orderByDesc('id_mat')->lockForUpdate()->first();
            $ultimaImparte = Imparte::orderByDesc('id_imp')->lockForUpdate()->first();
            $idMat = ($ultimaMateria->id_mat ?? 9000) + 1;
            $idImp = ($ultimaImparte->id_imp ?? 9000) + 1;

            Materia::create([
                'id_mat'        => $idMat,
                'id_us_reg'     => 1,
                'nombremat'     => $nombrePrograma,
                'nombre'        => $nombrePrograma,
                'estado'        => 1,
                'fecha_reg'     => now(),
            ]);

            Imparte::create([
                'id_imp'    => $idImp,
                'id_us_reg' => 1,
                'id_mat'    => $idMat,
                'estado'    => 1,
                'fecha_reg' => now(),
            ]);

            return $idImp;
        });
    }
}
