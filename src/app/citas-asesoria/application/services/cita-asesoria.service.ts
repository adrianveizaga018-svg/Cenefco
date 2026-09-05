import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CitaAsesoria, CitaAsesoriaListResponse, CitaAsesoriaListParams } from '../../domain/models/cita-asesoria.model';

@Injectable({ providedIn: 'root' })
export class CitaAsesoriaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/citas-asesoria';

  getAll(params: CitaAsesoriaListParams): Observable<CitaAsesoriaListResponse> {
    return this.http.get<CitaAsesoriaListResponse>(this.baseUrl, { params: params as any });
  }

  getById(id: number): Observable<CitaAsesoria> {
    return this.http.get<CitaAsesoria>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<CitaAsesoria>): Observable<CitaAsesoria> {
    return this.http.post<CitaAsesoria>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CitaAsesoria>): Observable<CitaAsesoria> {
    return this.http.put<CitaAsesoria>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
