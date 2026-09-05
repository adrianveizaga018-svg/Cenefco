import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CursoOpcion, ReporteCobrosParams, ReporteCobrosResponse } from '../../domain/models/reporte-cobros.model';

@Injectable({ providedIn: 'root' })
export class ReporteCobrosService {
  private http = inject(HttpClient);

  getReporte(p: ReporteCobrosParams): Observable<ReporteCobrosResponse> {
    let params = new HttpParams();
    if (p.id_imp)              params = params.set('id_imp', p.id_imp);
    if (p.periodo)             params = params.set('periodo', p.periodo);
    if (p.fecha)                params = params.set('fecha', p.fecha);
    if (p.fecha_inicio)        params = params.set('fecha_inicio', p.fecha_inicio);
    if (p.fecha_fin)           params = params.set('fecha_fin', p.fecha_fin);
    if (p.con_inactivos)       params = params.set('con_inactivos', '1');
    return this.http.get<ReporteCobrosResponse>('/api/v1/reportes/cuotas-curso', { params });
  }

  getCursos(): Observable<{ data: CursoOpcion[] }> {
    return this.http.get<{ data: CursoOpcion[] }>('/api/v1/inscripciones/cursos');
  }
}
