<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormularioInsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size = (int) $request->get('pageSize', 30);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_formularioins');
        if ($query) {
            $q->where('nombre_formins', 'like', "%{$query}%");
        }
        if ($request->has('id_imp')) {
            $q->where('id_imp', (int) $request->get('id_imp'));
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderBy('nombre_formins')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_formularioins')->where('id_formins', $id)->first();
        if (! $row) {
            abort(404, 'Formulario de inscripción no encontrado');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_formins' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_formularioins' => ['nullable', 'integer'],
            'nombre_formins' => ['required', 'string', 'max:200'],
            'asunto_email' => ['nullable', 'string', 'max:200'],
            'correo_remitente' => ['nullable', 'string', 'max:200'],
            'correo_control' => ['nullable', 'string', 'max:200'],
            'contenido_email' => ['nullable', 'string'],
            'id_us' => ['nullable', 'integer'],
            'texto_envio_correcto' => ['nullable', 'string'],
            'id_curso' => ['nullable', 'integer'],
            'id_imp' => ['nullable', 'integer'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_formularioins'] = $request->integer('num_formularioins', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_formularioins')->insert($data);

        return response()->json(DB::table('t_formularioins')->where('id_formins', $data['id_formins'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_formularioins')->where('id_formins', $id)->first();
        if (! $row) {
            abort(404, 'Formulario de inscripción no encontrado');
        }

        $data = $request->validate([
            'nombre_formins' => ['sometimes', 'required', 'string', 'max:200'],
            'asunto_email' => ['nullable', 'string', 'max:200'],
            'correo_remitente' => ['nullable', 'string', 'max:200'],
            'correo_control' => ['nullable', 'string', 'max:200'],
            'contenido_email' => ['nullable', 'string'],
            'texto_envio_correcto' => ['nullable', 'string'],
            'id_imp' => ['nullable', 'integer'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_formularioins')->where('id_formins', $id)->update($data);

        return response()->json(DB::table('t_formularioins')->where('id_formins', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_formularioins')->where('id_formins', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
