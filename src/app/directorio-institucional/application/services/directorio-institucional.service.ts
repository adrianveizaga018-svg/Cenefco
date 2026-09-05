import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { DirectorioInstitucional, DirectorioInstitucionalListResponse, DirectorioInstitucionalListParams } from '../../domain/models/directorio-institucional.model';

@Injectable({ providedIn: 'root' })
export class DirectorioInstitucionalService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/directorio-institucional';

  getAll(params: DirectorioInstitucionalListParams = {}): Observable<DirectorioInstitucionalListResponse> {
    return this.http.get<DirectorioInstitucionalListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<DirectorioInstitucional> {
    return this.http.get<DirectorioInstitucional>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<DirectorioInstitucional>): Observable<DirectorioInstitucional> {
    return this.http.post<DirectorioInstitucional>(this.baseUrl, data);
  }
  update(id: number, data: Partial<DirectorioInstitucional>): Observable<DirectorioInstitucional> {
    return this.http.put<DirectorioInstitucional>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
