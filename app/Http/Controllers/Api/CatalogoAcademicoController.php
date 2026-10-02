<?php

namespace App\Http\Controllers\Api;

use App\Domain\Catalogos\Contracts\CatalogoRepositoryInterface;
use App\Domain\Catalogos\Exceptions\CatalogoItemNotFoundException;
use App\Http\Controllers\Controller;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoAcademicoController extends Controller
{
    private const CATALOGOS = [
        'ciudades'               => ['table' => 't_ciudad',              'pk' => 'id_ciudad',            'label' => 'nombre_ciudad'],
        'tipos-pago'             => ['table' => 't_tipopago',            'pk' => 'id_tipopago',          'label' => 'titulo'],
        'tipos-universidad'      => ['table' => 't_tipouniversidad',     'pk' => 'id_tipouniversidad',   'label' => 'nombre_tipouniversidad'],
        'universidades'          => ['table' => 't_universidad',         'pk' => 'id_universidad',       'label' => 'nombre_universidad'],
        'ocupaciones'            => ['table' => 't_ocupacion',           'pk' => 'id_ocupacion',         'label' => 'nombre_ocupacion'],
        'profesiones'            => ['table' => 't_profesion',           'pk' => 'id_prof',              'label' => 'nombre_profesion'],
        'modalidades-titulacion' => ['table' => 't_modalidadtitulacion', 'pk' => 'id_modtit',            'label' => 'nombre_modalidad'],
        'categorias-plan'        => ['table' => 't_catplan',             'pk' => 'id_catplan',           'label' => 'titulocat'],
        'niveles'                => ['table' => 't_nivel',               'pk' => 'id_niv',               'label' => 'titulo'],
        'horas'                  => ['table' => 't_hora',                'pk' => 'id_hor',               'label' => 'titulo'],
        'menciones'              => ['table' => 't_mension',             'pk' => 'id_mension',           'label' => 'titulo_mension'],
        'meses-pago'             => ['table' => 't_mespago',             'pk' => 'id_mespago',           'label' => 'mes'],
        'tipos-programa'         => ['table' => 't_tipoprograma',        'pk' => 'id_tipoprograma',      'label' => 'nombre_tipoprograma'],
        'planes-doc'             => ['table' => 't_plandoc',             'pk' => 'id_plandoc',           'label' => 'titulo_plandoc'],
        'grupos-documento'       => ['table' => 't_grupodocumento',      'pk' => 'id_grupodocumento',    'label' => 'nombre_grupodocumento'],
        'grupos-permiso'         => ['table' => 't_grupopermiso',        'pk' => 'id_grupopermiso',      'label' => 'nombre_grupopermiso'],
        'categorias-articulo'    => ['table' => 't_categoria_articulo',  'pk' => 'id_cat_art',           'label' => 'nombre_cat'],
    ];

    public function __construct(
        private readonly CatalogoRepositoryInterface $repository
    ) {}

    private const CATALOGOS_PUBLICOS = ['ciudades', 'tipos-pago'];

    private function resolveCatalogo(string $catalogo): array
    {
        if (! array_key_exists($catalogo, self::CATALOGOS)) {
            throw new CatalogoItemNotFoundException($catalogo, 0);
        }

        return self::CATALOGOS[$catalogo];
    }

    public function indexPublico(Request $request, string $catalogo): JsonResponse
    {
        if (! in_array($catalogo, self::CATALOGOS_PUBLICOS, true)) {
            throw new CatalogoItemNotFoundException($catalogo, 0);
        }

        $cfg = $this->resolveCatalogo($catalogo);

        $pagination = PaginationDTO::fromArray([
            'pageIndex' => 1,
            'pageSize'  => 100,
            'sortKey'   => $cfg['label'],
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->repository->paginate($cfg['table'], $cfg['pk'], $cfg['label'], $pagination, false)
        );
    }

    public function index(Request $request, string $catalogo): JsonResponse
    {
        $cfg = $this->resolveCatalogo($catalogo);

        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 100),
            'query'     => $request->get('query', ''),
            'sortKey'   => $cfg['label'],
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->repository->paginate(
                $cfg['table'],
                $cfg['pk'],
                $cfg['label'],
                $pagination,
                $request->boolean('conInactivos', false),
            )
        );
    }

    public function show(Request $request, string $catalogo, int $id): JsonResponse
    {
        $cfg = $this->resolveCatalogo($catalogo);

        return response()->json(
            $this->repository->findById($cfg['table'], $cfg['pk'], $id)
        );
    }

    public function store(Request $request, string $catalogo): JsonResponse
    {
        $cfg = $this->resolveCatalogo($catalogo);
        $pk  = $cfg['pk'];
        $lbl = $cfg['label'];

        $validated = $request->validate([
            $pk        => ['required', 'integer', 'min:1'],
            'id_us_reg' => ['nullable', 'integer'],
            $lbl       => ['required', 'string', 'max:200'],
            'estado'   => ['nullable', 'integer', 'in:0,1'],
        ]);

        $data = array_merge($validated, [
            'id_us_reg' => $request->integer('id_us_reg', 0),
            'estado'    => $request->integer('estado', 1),
            'fecha_reg' => now(),
        ]);

        return response()->json(
            $this->repository->create($cfg['table'], $pk, $lbl, $data),
            201
        );
    }

    public function update(Request $request, string $catalogo, int $id): JsonResponse
    {
        $cfg = $this->resolveCatalogo($catalogo);
        $lbl = $cfg['label'];

        $data = $request->validate([
            $lbl     => ['sometimes', 'required', 'string', 'max:200'],
            'estado' => ['nullable', 'integer', 'in:0,1'],
        ]);

        return response()->json(
            $this->repository->update($cfg['table'], $cfg['pk'], $id, $data)
        );
    }

    public function destroy(Request $request, string $catalogo, int $id): JsonResponse
    {
        $cfg = $this->resolveCatalogo($catalogo);
        $this->repository->delete($cfg['table'], $cfg['pk'], $id);

        return response()->json(null, 204);
    }
}
