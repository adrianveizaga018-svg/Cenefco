<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\TareasAcademicas\Repositories\TareaAcademicaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TareaAcademicaController extends Controller
{
    public function __construct(
        private readonly TareaAcademicaRepositoryInterface $repository
    ) {}

    public function index(int $programaId): JsonResponse
    {
        $tareas = $this->repository->getByProgramaId($programaId);
        return response()->json($tareas);
    }

    public function store(Request $request, int $programaId): JsonResponse
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'requiere_archivo' => 'boolean',
        ]);

        $data['programa_id'] = $programaId;
        $data['estado'] = 'pendiente';
        $data['requiere_archivo'] = $data['requiere_archivo'] ?? false;

        $tarea = $this->repository->create($data);

        return response()->json($tarea, 201);
    }

    public function completar(Request $request, int $id): JsonResponse
    {
        $tarea = $this->repository->findById($id);
        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        if ($tarea->requiere_archivo) {
            $request->validate([
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            ]);
        }

        $updateData = [
            'estado' => 'completada',
            'completado_por_usuario_id' => $request->user()?->id ?? 1,
            'fecha_completado' => now(),
        ];

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('tareas_academicas', 'public');
            $updateData['archivo_url'] = $path;
        }

        $tarea = $this->repository->update($id, $updateData);

        return response()->json($tarea);
    }
}
