import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoPostgrado, TipoPostgradoListParams, TipoPostgradoListResponse, CreateTipoPostgradoPayload } from '../../domain/models/tipo-postgrado.model';

@Injectable({ providedIn: 'root' })
export class TipoPostgradoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/tipos-postgrado';

  getAll(params: TipoPostgradoListParams = {}): Observable<TipoPostgradoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.id_plan   != null) p = p.set('id_plan',   params.id_plan);
    p = p.set('conInactivos', 'true');
    return this.http.get<TipoPostgradoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<TipoPostgrado> {
    return this.http.get<TipoPostgrado>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateTipoPostgradoPayload): Observable<TipoPostgrado> {
    return this.http.post<TipoPostgrado>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateTipoPostgradoPayload>): Observable<TipoPostgrado> {
    return this.http.put<TipoPostgrado>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
