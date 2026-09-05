import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Ciudad, CiudadListParams, CiudadListResponse, CreateCiudadPayload } from '../../domain/models/ciudad.model';

@Injectable({ providedIn: 'root' })
export class CiudadService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/catalogo-academico/ciudades';

  getAll(params: CiudadListParams = {}): Observable<CiudadListResponse> {
    let p = new HttpParams().set('conInactivos', 'true');
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<CiudadListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<Ciudad> {
    return this.http.get<Ciudad>(`${this.base}/${id}`);
  }

  create(data: CreateCiudadPayload): Observable<Ciudad> {
    return this.http.post<Ciudad>(this.base, data);
  }

  update(id: number, data: Partial<CreateCiudadPayload>): Observable<Ciudad> {
    return this.http.put<Ciudad>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
