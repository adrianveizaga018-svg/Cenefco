import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Descargable, DescargableListParams, DescargableListResponse, CreateDescargablePayload } from '../../domain/models/descargable.model';

@Injectable({ providedIn: 'root' })
export class DescargableService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/descargables';

  getAll(params: DescargableListParams = {}): Observable<DescargableListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<DescargableListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Descargable> {
    return this.http.get<Descargable>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateDescargablePayload): Observable<Descargable> {
    return this.http.post<Descargable>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateDescargablePayload>): Observable<Descargable> {
    return this.http.put<Descargable>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
