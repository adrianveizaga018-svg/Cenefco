import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { GradoAcademico, GradoAcademicoListResponse, GradoAcademicoListParams } from '../../domain/models/grado-academico.model';

@Injectable({ providedIn: 'root' })
export class GradoAcademicoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/grados-academicos';

  getAll(params: GradoAcademicoListParams = {}): Observable<GradoAcademicoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<GradoAcademicoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<GradoAcademico> {
    return this.http.get<GradoAcademico>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<GradoAcademico>): Observable<GradoAcademico> {
    return this.http.post<GradoAcademico>(this.baseUrl, data);
  }

  update(id: number, data: Partial<GradoAcademico>): Observable<GradoAcademico> {
    return this.http.put<GradoAcademico>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
