export interface TareaAcademica {
  id: number;
  programa_id: number;
  titulo: string;
  descripcion: string | null;
  requiere_archivo: boolean;
  estado: 'pendiente' | 'completada';
  archivo_url: string | null;
  completado_por_usuario_id: number | null;
  fecha_completado: string | null;
  created_at?: string;
}

export interface CursoProgresoTareas {
  curso_id: number;
  curso_nombre: string;
  total_tareas: number;
  tareas_completadas: number;
  progreso: number;
}
