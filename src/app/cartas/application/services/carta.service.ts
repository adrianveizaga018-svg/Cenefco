import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Carta, CartaListParams, CartaListResponse, CreateCartaPayload } from '../../domain/models/carta.model';

@Injectable({ providedIn: 'root' })
export class CartaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/cartas';

  getAll(params: CartaListParams = {}): Observable<CartaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    p = p.set('conInactivos', 'true');
    return this.http.get<CartaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Carta> {
    return this.http.get<Carta>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateCartaPayload): Observable<Carta> {
    return this.http.post<Carta>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateCartaPayload>): Observable<Carta> {
    return this.http.put<Carta>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
