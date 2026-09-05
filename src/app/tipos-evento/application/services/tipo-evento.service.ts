import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoEvento, TipoEventoListResponse, TipoEventoListParams } from '../../domain/models/tipo-evento.model';

@Injectable({ providedIn: 'root' })
export class TipoEventoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/tipos-evento';

  getAll(params: TipoEventoListParams = {}): Observable<TipoEventoListResponse> {
    return this.http.get<TipoEventoListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<TipoEvento> {
    return this.http.get<TipoEvento>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TipoEvento>): Observable<TipoEvento> {
    return this.http.post<TipoEvento>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TipoEvento>): Observable<TipoEvento> {
    return this.http.put<TipoEvento>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
