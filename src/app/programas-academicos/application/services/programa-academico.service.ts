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
}
