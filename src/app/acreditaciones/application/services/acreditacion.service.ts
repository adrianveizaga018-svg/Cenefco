import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Acreditacion, AcreditacionListParams, AcreditacionListResponse, CreateAcreditacionPayload } from '../../domain/models/acreditacion.model';

@Injectable({ providedIn: 'root' })
export class AcreditacionService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/acreditaciones';

  getAll(params: AcreditacionListParams = {}): Observable<AcreditacionListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<AcreditacionListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Acreditacion> {
    return this.http.get<Acreditacion>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateAcreditacionPayload): Observable<Acreditacion> {
    return this.http.post<Acreditacion>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateAcreditacionPayload>): Observable<Acreditacion> {
    return this.http.put<Acreditacion>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
