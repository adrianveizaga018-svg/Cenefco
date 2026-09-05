import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TriviaPremio, TriviaPremioListResponse, TriviaPremioListParams } from '../../domain/models/trivia-premio.model';

@Injectable({ providedIn: 'root' })
export class TriviaPremioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/trivia-premios';

  getAll(params: TriviaPremioListParams = {}): Observable<TriviaPremioListResponse> {
    return this.http.get<TriviaPremioListResponse>(this.baseUrl, { params: params as Record<string, string | number | boolean> });
  }
  getById(id: number): Observable<TriviaPremio> {
    return this.http.get<TriviaPremio>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TriviaPremio>): Observable<TriviaPremio> {
    return this.http.post<TriviaPremio>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TriviaPremio>): Observable<TriviaPremio> {
    return this.http.put<TriviaPremio>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
