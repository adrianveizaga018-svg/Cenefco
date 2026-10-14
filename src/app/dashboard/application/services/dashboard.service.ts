import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, forkJoin, of } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { DashboardStats } from '../../domain/models/dashboard.model';
import { AuthService } from '../../../auth/application/services/auth.service';

@Injectable({ providedIn: 'root' })
export class DashboardService {
  private http = inject(HttpClient);
  private auth = inject(AuthService);

  /** Solo pide el contador si el rol puede verlo: los demás quedan en 0 sin errores 403 en consola. */
  private contador<T>(permiso: string, pedido: () => Observable<T>, vacio: T): Observable<T> {
    return this.auth.hasPermission(permiso) ? pedido().pipe(catchError(() => of(vacio))) : of(vacio);
  }

  getStats(): Observable<DashboardStats> {
    return forkJoin({
      inscripciones: this.contador('inscripciones.ver', () => this.http.get<{ total: number }>('/api/v1/inscripciones', {
        params: { pageSize: '1', pageIndex: '1', conInactivos: 'false' }
      }), { total: 0 }),
      cursos: this.contador('programas.ver', () => this.http.get<{ total: number }>('/api/v1/cursos', {
        params: { pageSize: '1', pageIndex: '1' }
      }), { total: 0 }),
      docentes: this.contador('docentes.ver', () => this.http.get<{ total: number }>('/api/v1/docentes-perfil', {
        params: { pageSize: '1', pageIndex: '1' }
      }), { total: 0 }),
      cursos_migrados: this.contador('cursos_migrados.ver',
        () => this.http.get<{ total_cursos: number; total_participantes: number; total_conversaciones_whatsapp: number }>('/api/v1/cursos-migrados/stats'),
        { total_cursos: 0, total_participantes: 0, total_conversaciones_whatsapp: 0 }),
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
