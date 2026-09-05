import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { NotaPrensa, NotaPrensaListParams, NotaPrensaListResponse, CreateNotaPrensaPayload } from '../../domain/models/nota-prensa.model';

@Injectable({ providedIn: 'root' })
export class NotaPrensaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/notas-prensa';

  getAll(params: NotaPrensaListParams = {}): Observable<NotaPrensaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<NotaPrensaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<NotaPrensa> {
    return this.http.get<NotaPrensa>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateNotaPrensaPayload): Observable<NotaPrensa> {
    return this.http.post<NotaPrensa>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateNotaPrensaPayload>): Observable<NotaPrensa> {
    return this.http.put<NotaPrensa>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
