import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CartaModelo, CartaModeloListParams, CartaModeloListResponse, CreateCartaModeloPayload } from '../../domain/models/carta-modelo.model';

@Injectable({ providedIn: 'root' })
export class CartaModeloService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/cartas-modelo';

  getAll(params: CartaModeloListParams = {}): Observable<CartaModeloListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    p = p.set('conInactivos', 'true');
    return this.http.get<CartaModeloListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<CartaModelo> {
    return this.http.get<CartaModelo>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateCartaModeloPayload): Observable<CartaModelo> {
    return this.http.post<CartaModelo>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateCartaModeloPayload>): Observable<CartaModelo> {
    return this.http.put<CartaModelo>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
