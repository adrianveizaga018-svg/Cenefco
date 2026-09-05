import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { DocentePerfil, DocentePerfilListParams, DocentePerfilListResponse, CreateDocentePerfilPayload, DocenteReporteMateriasResponse } from '../../domain/models/docente-perfil.model';

@Injectable({ providedIn: 'root' })
export class DocentePerfilService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/docentes-perfil';

  getAll(params: DocentePerfilListParams = {}): Observable<DocentePerfilListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<DocentePerfilListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<DocentePerfil> {
    return this.http.get<DocentePerfil>(`${this.baseUrl}/${id}`);
  }

  getBySlug(slug: string): Observable<DocentePerfil> {
    return this.http.get<DocentePerfil>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: CreateDocentePerfilPayload): Observable<DocentePerfil> {
    return this.http.post<DocentePerfil>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateDocentePerfilPayload>): Observable<DocentePerfil> {
    return this.http.put<DocentePerfil>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  getReporteMaterias(params: { gestion?: string; periodo?: string } = {}): Observable<DocenteReporteMateriasResponse> {
    let p = new HttpParams();
    if (params.gestion) p = p.set('gestion', params.gestion);
    if (params.periodo) p = p.set('periodo', params.periodo);
    return this.http.get<DocenteReporteMateriasResponse>(`${this.baseUrl}/reporte/materias`, { params: p });
  }
}
