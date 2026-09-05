import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Curso,
  CursoListParams,
  CursoListResponse,
  CreateCursoPayload,
  CategoriaCursoListResponse,
  TipoCursoListResponse,
  PlanDocListResponse,
  AlertaCobrosCurso,
  CursoEstadisticas,
  CursoEstadisticasDetalle,
  PeriodoEstadisticas,
  PlanCobrosCurso,
  ReporteEnviosDocumentos,
} from '../../domain/models/curso.model';

@Injectable({ providedIn: 'root' })
export class CursoService {
  private http = inject(HttpClient);
  private readonly baseUrl       = '/api/v1/cursos';
  private readonly categoriasUrl = '/api/v1/categorias-programa';
  private readonly tiposUrl      = '/api/v1/tipos-programa';
  private readonly planesDocUrl  = '/api/v1/planes-doc';

  getAll(params: CursoListParams = {}): Observable<CursoListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return this.http.get<CursoListResponse>(this.baseUrl, { params: httpParams });
  }

  getEstadisticas(periodo: PeriodoEstadisticas, fecha: string, fechaFin?: string): Observable<CursoEstadisticas> {
    const params: Record<string, string> = { periodo, fecha };
    if (fechaFin) params['fecha_fin'] = fechaFin;
    return this.http.get<CursoEstadisticas>(`${this.baseUrl}/estadisticas`, { params });
  }

  getEstadisticasDetalle(periodo: PeriodoEstadisticas, fecha: string, fechaFin?: string): Observable<CursoEstadisticasDetalle> {
    const params: Record<string, string> = { periodo, fecha };
    if (fechaFin) params['fecha_fin'] = fechaFin;
    return this.http.get<CursoEstadisticasDetalle>(`${this.baseUrl}/estadisticas/detalle`, { params });
  }

  getReporteEnviosDocumentos(fechaInicio?: string, fechaFin?: string): Observable<ReporteEnviosDocumentos> {
    const params: Record<string, string> = {};
    if (fechaInicio) params['fecha_inicio'] = fechaInicio;
    if (fechaFin)    params['fecha_fin']    = fechaFin;
    return this.http.get<ReporteEnviosDocumentos>(`${this.baseUrl}/reporte-envios-documentos`, { params });
  }

  obtenerOCrearPlanCobros(idPrograma: number): Observable<PlanCobrosCurso> {
    return this.http.post<PlanCobrosCurso>(`${this.baseUrl}/${idPrograma}/plan-cobros`, {});
  }

  getAlertasCobros(): Observable<AlertaCobrosCurso[]> {
    return this.http.get<AlertaCobrosCurso[]>(`${this.baseUrl}/alertas-cobros`);
  }

  getById(id: number): Observable<Curso> {
    return this.http.get<Curso>(`${this.baseUrl}/${id}`);
  }

  getBySlug(slug: string): Observable<Curso> {
    return this.http.get<Curso>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: CreateCursoPayload): Observable<Curso> {
    return this.http.post<Curso>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateCursoPayload>): Observable<Curso> {
    return this.http.put<Curso>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  getCategorias(): Observable<CategoriaCursoListResponse> {
    return this.http.get<CategoriaCursoListResponse>(this.categoriasUrl);
  }

  getTipos(): Observable<TipoCursoListResponse> {
    return this.http.get<TipoCursoListResponse>(this.tiposUrl);
  }

  getPlanesDoc(): Observable<PlanDocListResponse> {
    return this.http.get<PlanDocListResponse>(this.planesDocUrl, {
      params: { pageSize: '200' }
    });
  }

  getDocentes(id: number): Observable<{ id: number; nombre_completo: string; titulo_academico: string | null; especialidad: string | null; foto_url: string | null; email_publico: string | null }[]> {
    return this.http.get<{ id: number; nombre_completo: string; titulo_academico: string | null; especialidad: string | null; foto_url: string | null; email_publico: string | null }[]>(`${this.baseUrl}/${id}/docentes`);
  }

  attachDocente(cursoId: number, docenteId: number): Observable<void> {
    return this.http.post<void>(`${this.baseUrl}/${cursoId}/docentes`, { docente_id: docenteId });
  }

  detachDocente(cursoId: number, docenteId: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${cursoId}/docentes/${docenteId}`);
  }

  sincronizarMoodle(id: number): Observable<{ id: number; fullname: string; shortname: string }> {
    return this.http.post<{ id: number; fullname: string; shortname: string }>(
      `/api/v1/moodle/courses/from-curso/${id}`,
      {}
    );
  }

  exportarMoodleCSV(idImp: number): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/moodle-export`, {
      params: { id_imp: idImp },
      responseType: 'blob',
    });
  }
}
