<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\TareasAcademicas\Models\CatalogoTareaAcademica;
use Illuminate\Http\Request;

class CatalogoTareaController extends Controller
{
    public function index()
    {
        return response()->json(CatalogoTareaAcademica::orderBy('titulo')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255|unique:catalogo_tareas_academicas,titulo',
            'requiere_archivo' => 'boolean',
            'estado'           => 'boolean',
        ]);

        $catalogo = CatalogoTareaAcademica::create($validated);
        return response()->json($catalogo, 201);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255|unique:catalogo_tareas_academicas,titulo,' . $id,
            'requiere_archivo' => 'boolean',
            'estado'           => 'boolean',
        ]);

        $catalogo = CatalogoTareaAcademica::findOrFail($id);
        $catalogo->update($validated);
        
        return response()->json($catalogo);
    }

    public function destroy(int $id)
    {
        $catalogo = CatalogoTareaAcademica::findOrFail($id);
        $catalogo->delete();
        return response()->json(['message' => 'Eliminado exitosamente']);
    }
}
