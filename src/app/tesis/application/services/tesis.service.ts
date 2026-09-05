import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Tesis, TesisListParams, TesisListResponse, CreateTesisPayload } from '../../domain/models/tesis.model';

@Injectable({ providedIn: 'root' })
export class TesisService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/tesis';

  getAll(params: TesisListParams = {}): Observable<TesisListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<TesisListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Tesis> {
    return this.http.get<Tesis>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateTesisPayload): Observable<Tesis> {
    return this.http.post<Tesis>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateTesisPayload>): Observable<Tesis> {
    return this.http.put<Tesis>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
