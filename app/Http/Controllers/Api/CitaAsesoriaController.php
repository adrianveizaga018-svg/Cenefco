<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitaAsesoriaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size  = (int) $request->get('pageSize', 50);
        $page  = (int) $request->get('pageIndex', 1);

        $q = DB::table('web_cita_asesoria');
        if ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('nombres', 'like', "%{$query}%")
                    ->orWhere('apellidos', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('celular', 'like', "%{$query}%");
            });
        }
        if ($request->has('estado')) {
            $q->where('estado', $request->get('estado'));
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_solicitud')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('web_cita_asesoria')->where('id_cita_asesoria', $id)->first();
        if (! $row) {
            abort(404, 'Cita de asesoría no encontrada');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombres'         => ['required', 'string', 'max:255'],
            'apellidos'       => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'celular'         => ['nullable', 'string', 'max:20'],
            'mensaje'         => ['nullable', 'string'],
            'programa_interes'=> ['nullable', 'string', 'max:255'],
            'fecha_preferida' => ['nullable', 'date'],
            'hora_preferida'  => ['nullable', 'string', 'max:10'],
            'estado'          => ['nullable', 'string', 'max:50'],
        ]);
        $data['fecha_solicitud'] = now();
        $data['estado']          = $data['estado'] ?? 'pendiente';

        $id  = DB::table('web_cita_asesoria')->insertGetId($data);
        $row = DB::table('web_cita_asesoria')->where('id_cita_asesoria', $id)->first();

        return response()->json($row, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('web_cita_asesoria')->where('id_cita_asesoria', $id)->first();
        if (! $row) {
            abort(404, 'Cita de asesoría no encontrada');
        }

        $data = $request->validate([
            'nombres'         => ['sometimes', 'required', 'string', 'max:255'],
            'apellidos'       => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'celular'         => ['nullable', 'string', 'max:20'],
            'mensaje'         => ['nullable', 'string'],
            'programa_interes'=> ['nullable', 'string', 'max:255'],
            'fecha_preferida' => ['nullable', 'date'],
            'hora_preferida'  => ['nullable', 'string', 'max:10'],
            'estado'          => ['nullable', 'string', 'max:50'],
            'observacion'     => ['nullable', 'string'],
        ]);
        DB::table('web_cita_asesoria')->where('id_cita_asesoria', $id)->update($data);

        return response()->json(DB::table('web_cita_asesoria')->where('id_cita_asesoria', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = DB::table('web_cita_asesoria')->where('id_cita_asesoria', $id)->delete();
        if (! $deleted) {
            abort(404, 'Cita de asesoría no encontrada');
        }

        return response()->json(null, 204);
    }
}
