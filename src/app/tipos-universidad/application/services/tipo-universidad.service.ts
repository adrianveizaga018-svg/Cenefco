import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoUniversidad, TipoUniversidadListParams, TipoUniversidadListResponse, CreateTipoUniversidadPayload } from '../../domain/models/tipo-universidad.model';

@Injectable({ providedIn: 'root' })
export class TipoUniversidadService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/catalogo-academico/tipos-universidad';

  getAll(params: TipoUniversidadListParams = {}): Observable<TipoUniversidadListResponse> {
    let p = new HttpParams().set('conInactivos', 'true');
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<TipoUniversidadListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<TipoUniversidad> {
    return this.http.get<TipoUniversidad>(`${this.base}/${id}`);
  }

  create(data: CreateTipoUniversidadPayload): Observable<TipoUniversidad> {
    return this.http.post<TipoUniversidad>(this.base, data);
  }

  update(id: number, data: Partial<CreateTipoUniversidadPayload>): Observable<TipoUniversidad> {
    return this.http.put<TipoUniversidad>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
