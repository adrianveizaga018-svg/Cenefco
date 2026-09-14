import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ProgramaAcademico, ProgramaAcademicoListParams, ProgramaAcademicoListResponse, CreateProgramaAcademicoPayload } from '../../domain/models/programa-academico.model';

@Injectable({ providedIn: 'root' })
export class ProgramaAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/programas-academicos';

  getAll(params: ProgramaAcademicoListParams = {}): Observable<ProgramaAcademicoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex       != null) p = p.set('pageIndex',       params.pageIndex);
    if (params.pageSize        != null) p = p.set('pageSize',        params.pageSize);
    if (params.query)                   p = p.set('query',           params.query);
    if (params.id_tipoprograma != null) p = p.set('id_tipoprograma', params.id_tipoprograma);
    p = p.set('conInactivos', 'true');
    return this.http.get<ProgramaAcademicoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<ProgramaAcademico> {
    return this.http.get<ProgramaAcademico>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateProgramaAcademicoPayload): Observable<ProgramaAcademico> {
    return this.http.post<ProgramaAcademico>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateProgramaAcademicoPayload>): Observable<ProgramaAcademico> {
    return this.http.put<ProgramaAcademico>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  // --- Imparticiones (Versiones) ---
  getImparticiones(id: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.baseUrl}/${id}/imparticiones`);
  }

  createImparticion(id: number, data: any): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/${id}/imparticiones`, data);
  }

  updateImparticion(id: number, id_imp: number, data: any): Observable<any> {
    return this.http.put<any>(`${this.baseUrl}/${id}/imparticiones/${id_imp}`, data);
  }

  // --- Planes de Pago ---
  getPlanes(id: number): Observable<{planes_habilitados: any[], todos_los_planes: any[]}> {
    return this.http.get<any>(`${this.baseUrl}/${id}/planes`);
  }

  syncPlanes(id: number, planesIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/${id}/planes`, { planes: planesIds });
  }
}

