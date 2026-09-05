import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Autoridad, AutoridadListResponse, AutoridadListParams } from '../../domain/models/autoridad.model';

@Injectable({ providedIn: 'root' })
export class AutoridadService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/autoridades';

  getAll(params: AutoridadListParams = {}): Observable<AutoridadListResponse> {
    return this.http.get<AutoridadListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<Autoridad> {
    return this.http.get<Autoridad>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<Autoridad>): Observable<Autoridad> {
    return this.http.post<Autoridad>(this.baseUrl, data);
  }
  update(id: number, data: Partial<Autoridad>): Observable<Autoridad> {
    return this.http.put<Autoridad>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
