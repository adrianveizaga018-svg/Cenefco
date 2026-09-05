import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Secretaria, SecretariaListResponse, SecretariaListParams } from '../../domain/models/secretaria.model';

@Injectable({ providedIn: 'root' })
export class SecretariaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/secretarias';

  getAll(params: SecretariaListParams = {}): Observable<SecretariaListResponse> {
    return this.http.get<SecretariaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<Secretaria> {
    return this.http.get<Secretaria>(`${this.baseUrl}/${id}`);
  }
  getBySlug(slug: string): Observable<Secretaria> {
    return this.http.get<Secretaria>(`${this.baseUrl}/slug/${slug}`);
  }
  create(data: Partial<Secretaria>): Observable<Secretaria> {
    return this.http.post<Secretaria>(this.baseUrl, data);
  }
  update(id: number, data: Partial<Secretaria>): Observable<Secretaria> {
    return this.http.put<Secretaria>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
