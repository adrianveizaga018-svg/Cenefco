import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Suscriptor, SuscriptorListParams, SuscriptorListResponse } from '../../domain/models/suscriptor.model';

@Injectable({ providedIn: 'root' })
export class SuscriptorService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/suscriptores';

  getAll(params: SuscriptorListParams = {}): Observable<SuscriptorListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<SuscriptorListResponse>(this.baseUrl, { params: p });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
