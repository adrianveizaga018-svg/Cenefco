<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebMenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size  = (int) $request->get('pageSize', 20);
        $page  = (int) $request->get('pageIndex', 1);

        $q = DB::table('web_menus');
        if ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        }

        $total = $q->count();
        $data  = $q->orderBy('id')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $menu = DB::table('web_menus')->find($id);
        if (! $menu) abort(404, 'Menú no encontrado');
        return response()->json($menu);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:80', 'unique:web_menus,nombre'],
            'descripcion' => ['nullable', 'string', 'max:100'],
            'activo'      => ['nullable', 'boolean'],
        ]);
        $data['activo'] = $request->boolean('activo', true);

        $id = DB::table('web_menus')->insertGetId($data);
        return response()->json(DB::table('web_menus')->find($id), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (! DB::table('web_menus')->where('id', $id)->exists()) abort(404, 'Menú no encontrado');

        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:80', "unique:web_menus,nombre,{$id}"],
            'descripcion' => ['nullable', 'string', 'max:100'],
            'activo'      => ['nullable', 'boolean'],
        ]);
        $data['activo'] = $request->boolean('activo', true);

        DB::table('web_menus')->where('id', $id)->update($data);
        return response()->json(DB::table('web_menus')->find($id));
    }

    public function destroy(int $id): JsonResponse
    {
        if (! DB::table('web_menus')->where('id', $id)->delete()) abort(404, 'Menú no encontrado');
        return response()->json(null, 204);
    }

    public function itemsByNombre(string $nombre): JsonResponse
    {
        $menu = DB::table('web_menus')->where('nombre', $nombre)->where('activo', true)->first();
        if (! $menu) abort(404, 'Menú no encontrado');

        $items = DB::table('web_menu_items')
            ->where('menu_id', $menu->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        return response()->json(['data' => $items, 'total' => $items->count()]);
    }
}
