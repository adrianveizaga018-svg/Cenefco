import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Organigrama, OrganigramaListResponse, OrganigramaListParams } from '../../domain/models/organigrama.model';

@Injectable({ providedIn: 'root' })
export class OrganigramaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/organigramas';

  getAll(params: OrganigramaListParams = {}): Observable<OrganigramaListResponse> {
    return this.http.get<OrganigramaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<Organigrama> {
    return this.http.get<Organigrama>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<Organigrama>): Observable<Organigrama> {
    return this.http.post<Organigrama>(this.baseUrl, data);
  }
  update(id: number, data: Partial<Organigrama>): Observable<Organigrama> {
    return this.http.put<Organigrama>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
