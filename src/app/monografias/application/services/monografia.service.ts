import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Monografia, MonografiaListParams, MonografiaListResponse, CreateMonografiaPayload } from '../../domain/models/monografia.model';

@Injectable({ providedIn: 'root' })
export class MonografiaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/monografias';

  getAll(params: MonografiaListParams = {}): Observable<MonografiaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<MonografiaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Monografia> {
    return this.http.get<Monografia>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateMonografiaPayload): Observable<Monografia> {
    return this.http.post<Monografia>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateMonografiaPayload>): Observable<Monografia> {
    return this.http.put<Monografia>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
