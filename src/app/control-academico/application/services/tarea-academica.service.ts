import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { CursoProgresoTareas, TareaAcademica } from '../../domain/models/tarea-academica.model';

@Injectable({
  providedIn: 'root'
})
export class TareaAcademicaService {
  private http = inject(HttpClient);
  private base = '/api/v1';

  getControlAcademicoDashboard(): Observable<CursoProgresoTareas[]> {
    return this.http.get<CursoProgresoTareas[]>(`${this.base}/dashboard/control-academico`);
  }

  getTareasByCurso(cursoId: number): Observable<TareaAcademica[]> {
    return this.http.get<TareaAcademica[]>(`${this.base}/cursos/${cursoId}/tareas`);
  }

  storeTarea(cursoId: number, data: any): Observable<TareaAcademica> {
    return this.http.post<TareaAcademica>(`${this.base}/cursos/${cursoId}/tareas`, data);
  }

  completarTarea(tareaId: number, file: File | null): Observable<TareaAcademica> {
    const formData = new FormData();
    if (file) {
      formData.append('archivo', file);
    }
    return this.http.post<TareaAcademica>(`${this.base}/tareas/${tareaId}/completar`, formData);
  }
}

