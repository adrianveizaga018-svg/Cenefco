import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CifraInstitucional, CifraInstitucionalListResponse, CifraInstitucionalListParams } from '../../domain/models/cifra-institucional.model';

@Injectable({ providedIn: 'root' })
export class CifraInstitucionalService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/cifras-institucionales';

  getAll(params: CifraInstitucionalListParams = {}): Observable<CifraInstitucionalListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<CifraInstitucionalListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<CifraInstitucional> {
    return this.http.get<CifraInstitucional>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<CifraInstitucional>): Observable<CifraInstitucional> {
    return this.http.post<CifraInstitucional>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CifraInstitucional>): Observable<CifraInstitucional> {
    return this.http.put<CifraInstitucional>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
