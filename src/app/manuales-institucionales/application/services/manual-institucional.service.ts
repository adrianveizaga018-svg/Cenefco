import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { ManualInstitucional, ManualInstitucionalListResponse, ManualInstitucionalListParams } from '../../domain/models/manual-institucional.model';

@Injectable({ providedIn: 'root' })
export class ManualInstitucionalService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/manuales-institucionales';

  getAll(params: ManualInstitucionalListParams = {}): Observable<ManualInstitucionalListResponse> {
    return this.http.get<ManualInstitucionalListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<ManualInstitucional> {
    return this.http.get<ManualInstitucional>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<ManualInstitucional>): Observable<ManualInstitucional> {
    return this.http.post<ManualInstitucional>(this.baseUrl, data);
  }
  update(id: number, data: Partial<ManualInstitucional>): Observable<ManualInstitucional> {
    return this.http.put<ManualInstitucional>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
