<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanDocController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $size = (int) $request->get('pageSize', 100);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_plandoc');

        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        if ($search = $request->get('query')) {
            $q->where('titulo_plandoc', 'like', "%{$search}%");
        }

        $total = $q->count();
        $data  = $q->orderBy('id_plandoc')
                   ->offset(($page - 1) * $size)
                   ->limit($size)
                   ->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }
}
