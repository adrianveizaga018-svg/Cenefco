import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TipoPago, TipoPagoListParams, TipoPagoListResponse, CreateTipoPagoPayload } from '../../domain/models/tipo-pago.model';

@Injectable({ providedIn: 'root' })
export class TipoPagoService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/catalogo-academico/tipos-pago';

  getAll(params: TipoPagoListParams = {}): Observable<TipoPagoListResponse> {
    let p = new HttpParams().set('conInactivos', 'true');
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<TipoPagoListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<TipoPago> {
    return this.http.get<TipoPago>(`${this.base}/${id}`);
  }

  create(data: CreateTipoPagoPayload): Observable<TipoPago> {
    return this.http.post<TipoPago>(this.base, data);
  }

  update(id: number, data: Partial<CreateTipoPagoPayload>): Observable<TipoPago> {
    return this.http.put<TipoPago>(`${this.base}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
