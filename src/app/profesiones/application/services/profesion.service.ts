import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Profesion, ProfesionListParams, ProfesionListResponse, CreateProfesionPayload } from '../../domain/models/profesion.model';

@Injectable({ providedIn: 'root' })
export class ProfesionService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/catalogo-academico/profesiones';

  getAll(params: ProfesionListParams = {}): Observable<ProfesionListResponse> {
    let p = new HttpParams().set('conInactivos', 'true');
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<ProfesionListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<Profesion> {
    return this.http.get<Profesion>(`${this.base}/${id}`);
  }

  create(data: CreateProfesionPayload): Observable<Profesion> {
    return this.http.post<Profesion>(this.base, data);
  }

  update(id: number, data: Partial<CreateProfesionPayload>): Observable<Profesion> {
    return this.http.put<Profesion>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
