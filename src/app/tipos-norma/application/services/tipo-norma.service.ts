import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoNorma, TipoNormaListResponse, TipoNormaListParams } from '../../domain/models/tipo-norma.model';

@Injectable({ providedIn: 'root' })
export class TipoNormaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/tipos-norma';

  getAll(params: TipoNormaListParams = {}): Observable<TipoNormaListResponse> {
    return this.http.get<TipoNormaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<TipoNorma> {
    return this.http.get<TipoNorma>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TipoNorma>): Observable<TipoNorma> {
    return this.http.post<TipoNorma>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TipoNorma>): Observable<TipoNorma> {
    return this.http.put<TipoNorma>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
