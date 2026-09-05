import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Ayuda, AyudaListParams, AyudaListResponse, CreateAyudaPayload } from '../../domain/models/ayuda.model';

@Injectable({ providedIn: 'root' })
export class AyudaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/ayudas';

  getAll(params: AyudaListParams = {}): Observable<AyudaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    p = p.set('conInactivos', 'true');
    return this.http.get<AyudaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Ayuda> {
    return this.http.get<Ayuda>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateAyudaPayload): Observable<Ayuda> {
    return this.http.post<Ayuda>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateAyudaPayload>): Observable<Ayuda> {
    return this.http.put<Ayuda>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
