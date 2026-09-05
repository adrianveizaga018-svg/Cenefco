import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Expedido, ExpedidoListResponse, ExpedidoListParams } from '../../domain/models/expedido.model';

@Injectable({ providedIn: 'root' })
export class ExpedidoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/expedido';

  getAll(params: ExpedidoListParams = {}): Observable<ExpedidoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<ExpedidoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Expedido> {
    return this.http.get<Expedido>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<Expedido>): Observable<Expedido> {
    return this.http.post<Expedido>(this.baseUrl, data);
  }

  update(id: number, data: Partial<Expedido>): Observable<Expedido> {
    return this.http.put<Expedido>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
