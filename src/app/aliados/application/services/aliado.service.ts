import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Aliado, AliadoListParams, AliadoListResponse, CreateAliadoPayload } from '../../domain/models/aliado.model';

@Injectable({ providedIn: 'root' })
export class AliadoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/aliados';

  getAll(params: AliadoListParams = {}): Observable<AliadoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<AliadoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Aliado> {
    return this.http.get<Aliado>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateAliadoPayload): Observable<Aliado> {
    return this.http.post<Aliado>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateAliadoPayload>): Observable<Aliado> {
    return this.http.put<Aliado>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
