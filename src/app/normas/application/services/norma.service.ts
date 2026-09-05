import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Norma, NormaListResponse, NormaListParams } from '../../domain/models/norma.model';

@Injectable({ providedIn: 'root' })
export class NormaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/normas';

  getAll(params: NormaListParams = {}): Observable<NormaListResponse> {
    return this.http.get<NormaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<Norma> {
    return this.http.get<Norma>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<Norma>): Observable<Norma> {
    return this.http.post<Norma>(this.baseUrl, data);
  }
  update(id: number, data: Partial<Norma>): Observable<Norma> {
    return this.http.put<Norma>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
