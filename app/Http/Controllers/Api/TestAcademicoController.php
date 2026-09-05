<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestAcademicoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size = (int) $request->get('pageSize', 30);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_test');
        if ($query) {
            $q->where('nombretest', 'like', "%{$query}%");
        }
        if ($request->has('habilitar_test')) {
            $q->where('habilitar_test', $request->get('habilitar_test'));
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderBy('nombretest')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_test')->where('id_test', $id)->first();
        if (! $row) {
            abort(404, 'Test no encontrado');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_test' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_test' => ['nullable', 'integer'],
            'nombretest' => ['required', 'string', 'max:200'],
            'asunto_email' => ['nullable', 'string', 'max:200'],
            'correo_remitente' => ['nullable', 'string', 'max:200'],
            'correo_control' => ['nullable', 'string', 'max:200'],
            'contenido_email' => ['nullable', 'string'],
            'id_us' => ['nullable', 'integer'],
            'texto_envio_correcto' => ['nullable', 'string'],
            'descripcion_test' => ['nullable', 'string'],
            'introduccion_test' => ['nullable', 'string'],
            'habilitar_test' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_test'] = $request->integer('num_test', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_test')->insert($data);

        return response()->json(DB::table('t_test')->where('id_test', $data['id_test'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_test')->where('id_test', $id)->first();
        if (! $row) {
            abort(404, 'Test no encontrado');
        }

        $data = $request->validate([
            'nombretest' => ['sometimes', 'required', 'string', 'max:200'],
            'asunto_email' => ['nullable', 'string', 'max:200'],
            'correo_remitente' => ['nullable', 'string', 'max:200'],
            'correo_control' => ['nullable', 'string', 'max:200'],
            'contenido_email' => ['nullable', 'string'],
            'texto_envio_correcto' => ['nullable', 'string'],
            'descripcion_test' => ['nullable', 'string'],
            'introduccion_test' => ['nullable', 'string'],
            'habilitar_test' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_test')->where('id_test', $id)->update($data);

        return response()->json(DB::table('t_test')->where('id_test', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_test')->where('id_test', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
