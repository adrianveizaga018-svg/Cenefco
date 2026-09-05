import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { HitoInstitucional, HitoInstitucionalListResponse, HitoInstitucionalListParams } from '../../domain/models/hito-institucional.model';

@Injectable({ providedIn: 'root' })
export class HitoInstitucionalService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/hitos-institucionales';

  getAll(params: HitoInstitucionalListParams = {}): Observable<HitoInstitucionalListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<HitoInstitucionalListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<HitoInstitucional> {
    return this.http.get<HitoInstitucional>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<HitoInstitucional>): Observable<HitoInstitucional> {
    return this.http.post<HitoInstitucional>(this.baseUrl, data);
  }

  update(id: number, data: Partial<HitoInstitucional>): Observable<HitoInstitucional> {
    return this.http.put<HitoInstitucional>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
