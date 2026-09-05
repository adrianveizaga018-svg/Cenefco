import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { HistoriaInstitucional, HistoriaInstitucionalListResponse, HistoriaInstitucionalListParams } from '../../domain/models/historia-institucional.model';

@Injectable({ providedIn: 'root' })
export class HistoriaInstitucionalService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/historia-municipio';

  getAll(params: HistoriaInstitucionalListParams = {}): Observable<HistoriaInstitucionalListResponse> {
    return this.http.get<HistoriaInstitucionalListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<HistoriaInstitucional> {
    return this.http.get<HistoriaInstitucional>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<HistoriaInstitucional>): Observable<HistoriaInstitucional> {
    return this.http.post<HistoriaInstitucional>(this.baseUrl, data);
  }
  update(id: number, data: Partial<HistoriaInstitucional>): Observable<HistoriaInstitucional> {
    return this.http.put<HistoriaInstitucional>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
