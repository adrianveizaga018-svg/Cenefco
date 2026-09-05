import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { SugerenciaReclamo, SugerenciaReclamoListResponse, SugerenciaReclamoListParams } from '../../domain/models/sugerencia-reclamo.model';

@Injectable({ providedIn: 'root' })
export class SugerenciaReclamoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/sugerencias-reclamos';

  getAll(params: SugerenciaReclamoListParams = {}): Observable<SugerenciaReclamoListResponse> {
    return this.http.get<SugerenciaReclamoListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<SugerenciaReclamo> {
    return this.http.get<SugerenciaReclamo>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<SugerenciaReclamo>): Observable<SugerenciaReclamo> {
    return this.http.post<SugerenciaReclamo>(this.baseUrl, data);
  }
  update(id: number, data: Partial<SugerenciaReclamo>): Observable<SugerenciaReclamo> {
    return this.http.put<SugerenciaReclamo>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
