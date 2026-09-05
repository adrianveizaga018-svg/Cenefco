import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CartaGenerada, CartaGeneradaListParams, CartaGeneradaListResponse, CreateCartaGeneradaPayload } from '../../domain/models/carta-generada.model';

@Injectable({ providedIn: 'root' })
export class CartaGeneradaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/cartas-generadas';

  getAll(params: CartaGeneradaListParams = {}): Observable<CartaGeneradaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    p = p.set('conInactivos', 'true');
    return this.http.get<CartaGeneradaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<CartaGenerada> {
    return this.http.get<CartaGenerada>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateCartaGeneradaPayload): Observable<CartaGenerada> {
    return this.http.post<CartaGenerada>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateCartaGeneradaPayload>): Observable<CartaGenerada> {
    return this.http.put<CartaGenerada>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
