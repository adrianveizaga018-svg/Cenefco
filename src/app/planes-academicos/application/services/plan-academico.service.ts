import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { PlanAcademico, PlanAcademicoListParams, PlanAcademicoListResponse, CreatePlanAcademicoPayload } from '../../domain/models/plan-academico.model';

@Injectable({ providedIn: 'root' })
export class PlanAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/planes-academicos';

  getAll(params: PlanAcademicoListParams = {}): Observable<PlanAcademicoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<PlanAcademicoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<PlanAcademico> {
    return this.http.get<PlanAcademico>(`${this.baseUrl}/${id}`);
  }

  create(data: CreatePlanAcademicoPayload): Observable<PlanAcademico> {
    return this.http.post<PlanAcademico>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreatePlanAcademicoPayload>): Observable<PlanAcademico> {
    return this.http.put<PlanAcademico>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
