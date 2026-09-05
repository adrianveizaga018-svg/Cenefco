import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, forkJoin, of } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { DashboardStats } from '../../domain/models/dashboard.model';

@Injectable({ providedIn: 'root' })
export class DashboardService {
  private http = inject(HttpClient);

  getStats(): Observable<DashboardStats> {
    return forkJoin({
      inscripciones: this.http.get<{ total: number }>('/api/v1/inscripciones', {
        params: { pageSize: '1', pageIndex: '1', conInactivos: 'false' }
      }).pipe(catchError(() => of({ total: 0 }))),
      cursos: this.http.get<{ total: number }>('/api/v1/cursos', {
        params: { pageSize: '1', pageIndex: '1' }
      }).pipe(catchError(() => of({ total: 0 }))),
      docentes: this.http.get<{ total: number }>('/api/v1/docentes-perfil', {
        params: { pageSize: '1', pageIndex: '1' }
      }).pipe(catchError(() => of({ total: 0 }))),
      cursos_migrados: this.http.get<{ total_cursos: number; total_participantes: number; total_conversaciones_whatsapp: number }>('/api/v1/cursos-migrados/stats')
        .pipe(catchError(() => of({ total_cursos: 0, total_participantes: 0, total_conversaciones_whatsapp: 0 }))),
    }).pipe(
      map(r => ({
        resumen: {
          total_inscripciones:    r.inscripciones.total,
          total_cursos:           r.cursos.total,
          total_docentes:         r.docentes.total,
          cursos_migrados:        r.cursos_migrados.total_cursos,
          participantes_migrados: r.cursos_migrados.total_participantes,
          conversaciones_whatsapp: r.cursos_migrados.total_conversaciones_whatsapp,
        },
      }))
    );
  }
}
