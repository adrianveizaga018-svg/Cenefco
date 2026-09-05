import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CalendarioAcademico, CalendarioAcademicoListParams, CalendarioAcademicoListResponse } from '../../domain/models/calendario-academico.model';

@Injectable({ providedIn: 'root' })
export class CalendarioAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/calendario-academico';

  getAll(p: CalendarioAcademicoListParams = {}): Observable<CalendarioAcademicoListResponse> {
    let params = new HttpParams();
    if (p.pageIndex != null) params = params.set('pageIndex', p.pageIndex);
    if (p.pageSize  != null) params = params.set('pageSize',  p.pageSize);
    if (p.query)              params = params.set('query',    p.query);
    if (p.tipo)               params = params.set('tipo',     p.tipo);
    return this.http.get<CalendarioAcademicoListResponse>(this.baseUrl, { params });
  }

  getById(id: number): Observable<CalendarioAcademico> {
    return this.http.get<CalendarioAcademico>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<CalendarioAcademico>): Observable<CalendarioAcademico> {
    return this.http.post<CalendarioAcademico>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CalendarioAcademico>): Observable<CalendarioAcademico> {
    return this.http.put<CalendarioAcademico>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  private exportParams(p: CalendarioAcademicoListParams = {}): HttpParams {
    let params = new HttpParams();
    if (p.query) params = params.set('query', p.query);
    if (p.tipo)  params = params.set('tipo',  p.tipo);
    return params;
  }

  exportPdf(p: CalendarioAcademicoListParams = {}): Observable<Blob> {
    return this.http.get('/api/v1/calendario-academico-export/pdf', {
      params: this.exportParams(p),
      responseType: 'blob',
    });
  }

  exportExcel(p: CalendarioAcademicoListParams = {}): Observable<Blob> {
    return this.http.get('/api/v1/calendario-academico-export/excel', {
      params: this.exportParams(p),
      responseType: 'blob',
    });
  }
}
