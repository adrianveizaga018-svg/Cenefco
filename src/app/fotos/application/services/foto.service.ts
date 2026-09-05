import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Foto, FotoListParams, FotoListResponse, CreateFotoPayload } from '../../domain/models/foto.model';

@Injectable({ providedIn: 'root' })
export class FotoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/fotos';

  getAll(params: FotoListParams = {}): Observable<FotoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<FotoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Foto> {
    return this.http.get<Foto>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateFotoPayload): Observable<Foto> {
    return this.http.post<Foto>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateFotoPayload>): Observable<Foto> {
    return this.http.put<Foto>(`${this.baseUrl}/${id}`, data);
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
