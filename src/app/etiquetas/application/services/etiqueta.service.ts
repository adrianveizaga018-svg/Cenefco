import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Etiqueta, EtiquetaListResponse, EtiquetaListParams } from '../../domain/models/etiqueta.model';

@Injectable({ providedIn: 'root' })
export class EtiquetaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/etiquetas';

  getAll(params: EtiquetaListParams = {}): Observable<EtiquetaListResponse> {
    return this.http.get<EtiquetaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }

  getById(id: number): Observable<Etiqueta> {
    return this.http.get<Etiqueta>(`${this.baseUrl}/${id}`);
  }
  getBySlug(slug: string): Observable<Etiqueta> {
    return this.http.get<Etiqueta>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: Partial<Etiqueta>): Observable<Etiqueta> {
    return this.http.post<Etiqueta>(this.baseUrl, data);
  }

  update(id: number, data: Partial<Etiqueta>): Observable<Etiqueta> {
    return this.http.put<Etiqueta>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
