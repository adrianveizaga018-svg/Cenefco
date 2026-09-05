import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Area, AreaListParams, AreaListResponse, CreateAreaPayload } from '../../domain/models/area.model';

@Injectable({ providedIn: 'root' })
export class AreaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/areas';

  getAll(params: AreaListParams = {}): Observable<AreaListResponse> {
    return this.http.get<AreaListResponse>(this.baseUrl, { params: params as any });
  }

  getById(id: number): Observable<Area> {
    return this.http.get<Area>(`${this.baseUrl}/${id}`);
  }
  getBySlug(slug: string): Observable<Area> {
    return this.http.get<Area>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: CreateAreaPayload): Observable<Area> {
    return this.http.post<Area>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateAreaPayload>): Observable<Area> {
    return this.http.put<Area>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
