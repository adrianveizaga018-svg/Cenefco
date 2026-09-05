import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  SpeechVentas,
  SpeechVentasListResponse,
  SpeechVentasParams,
} from '../../domain/models/speech-ventas.model';

@Injectable({ providedIn: 'root' })
export class SpeechVentasService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/speeches-ventas';

  getAll(params: SpeechVentasParams = {}): Observable<SpeechVentasListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex',  params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',   params.pageSize);
    if (params.query)              p = p.set('query',      params.query);
    if (params.categoria)          p = p.set('categoria',  params.categoria);
    if (params.activo != null)     p = p.set('activo',     params.activo);
    return this.http.get<SpeechVentasListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<SpeechVentas> {
    return this.http.get<SpeechVentas>(`${this.base}/${id}`);
  }

  create(data: Partial<SpeechVentas>): Observable<SpeechVentas> {
    return this.http.post<SpeechVentas>(this.base, data);
  }

  update(id: number, data: Partial<SpeechVentas>): Observable<SpeechVentas> {
    return this.http.put<SpeechVentas>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  toggleActivo(id: number): Observable<SpeechVentas> {
    return this.http.patch<SpeechVentas>(`${this.base}/${id}/toggle`, {});
  }

  getCategorias(): Observable<string[]> {
    return this.http.get<string[]>(`${this.base}/categorias`);
  }
}
