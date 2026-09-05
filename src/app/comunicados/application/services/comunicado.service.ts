import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Comunicado, ComunicadoListResponse, ComunicadoListParams } from '../../domain/models/comunicado.model';

@Injectable({ providedIn: 'root' })
export class ComunicadoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/comunicados';

  getAll(params: ComunicadoListParams = {}): Observable<ComunicadoListResponse> {
    return this.http.get<ComunicadoListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<Comunicado> {
    return this.http.get<Comunicado>(`${this.baseUrl}/${id}`);
  }
  getBySlug(slug: string): Observable<Comunicado> {
    return this.http.get<Comunicado>(`${this.baseUrl}/slug/${slug}`);
  }
  create(data: Partial<Comunicado>): Observable<Comunicado> {
    return this.http.post<Comunicado>(this.baseUrl, data);
  }
  update(id: number, data: Partial<Comunicado>): Observable<Comunicado> {
    return this.http.put<Comunicado>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
