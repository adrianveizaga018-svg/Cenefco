import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Redireccion, RedireccionListParams, RedireccionListResponse, CreateRedireccionPayload } from '../../domain/models/redireccion.model';

@Injectable({ providedIn: 'root' })
export class RedireccionService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/redirecciones';

  getAll(params: RedireccionListParams = {}): Observable<RedireccionListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<RedireccionListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Redireccion> {
    return this.http.get<Redireccion>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateRedireccionPayload): Observable<Redireccion> {
    return this.http.post<Redireccion>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateRedireccionPayload>): Observable<Redireccion> {
    return this.http.put<Redireccion>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
