import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { FechaDoc, FechaDocListParams, FechaDocListResponse, CreateFechaDocPayload } from '../../domain/models/fecha-doc.model';

@Injectable({ providedIn: 'root' })
export class FechaDocService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/fechas-doc';
  getAll(params: FechaDocListParams = {}): Observable<FechaDocListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',  params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',   params.pageSize);
    if (params.id_plandoc != null) p = p.set('id_plandoc', params.id_plandoc);
    p = p.set('conInactivos', 'true');
    return this.http.get<FechaDocListResponse>(this.baseUrl, { params: p });
  }
  getById(id: number): Observable<FechaDoc> { return this.http.get<FechaDoc>(`${this.baseUrl}/${id}`); }
  create(data: CreateFechaDocPayload): Observable<FechaDoc> { return this.http.post<FechaDoc>(this.baseUrl, data); }
  update(id: number, data: Partial<CreateFechaDocPayload>): Observable<FechaDoc> { return this.http.put<FechaDoc>(`${this.baseUrl}/${id}`, data); }
  delete(id: number): Observable<void> { return this.http.delete<void>(`${this.baseUrl}/${id}`); }
}
