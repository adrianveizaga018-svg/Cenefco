import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Universidad, UniversidadListResponse, UniversidadListParams } from '../../domain/models/universidad.model';

@Injectable({ providedIn: 'root' })
export class UniversidadService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/universidades';

  getAll(params: UniversidadListParams): Observable<UniversidadListResponse> {
    return this.http.get<UniversidadListResponse>(this.baseUrl, { params: params as any });
  }

  getById(id: number): Observable<Universidad> {
    return this.http.get<Universidad>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<Universidad>): Observable<Universidad> {
    return this.http.post<Universidad>(this.baseUrl, data);
  }

  update(id: number, data: Partial<Universidad>): Observable<Universidad> {
    return this.http.put<Universidad>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
