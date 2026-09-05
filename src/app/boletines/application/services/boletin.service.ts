import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Boletin, BoletinListParams, BoletinListResponse, CreateBoletinPayload } from '../../domain/models/boletin.model';

@Injectable({ providedIn: 'root' })
export class BoletinService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/boletines';

  getAll(params: BoletinListParams = {}): Observable<BoletinListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<BoletinListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Boletin> {
    return this.http.get<Boletin>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateBoletinPayload): Observable<Boletin> {
    return this.http.post<Boletin>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateBoletinPayload>): Observable<Boletin> {
    return this.http.put<Boletin>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  uploadImage(file: File): Observable<{ url: string }> {
    const fd = new FormData();
    fd.append('file', file);
    return this.http.post<{ url: string }>('/api/v1/upload/image', fd);
  }
}
