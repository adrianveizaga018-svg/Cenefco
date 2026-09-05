import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CreateVendedorPayload, Vendedor, VendedorComisionDetalle, VendedorListParams, VendedorListResponse } from '../../domain/models/vendedor.model';

@Injectable({ providedIn: 'root' })
export class VendedorService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/vendedores';

  getAll(p: VendedorListParams = {}): Observable<VendedorListResponse> {
    let params = new HttpParams();
    if (p.pageIndex != null) params = params.set('pageIndex', p.pageIndex);
    if (p.pageSize  != null) params = params.set('pageSize',  p.pageSize);
    if (p.query)              params = params.set('query',    p.query);
    if (p.activo)             params = params.set('activo',   p.activo);
    return this.http.get<VendedorListResponse>(this.baseUrl, { params });
  }

  getById(id: number): Observable<Vendedor> {
    return this.http.get<Vendedor>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateVendedorPayload): Observable<Vendedor> {
    return this.http.post<Vendedor>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateVendedorPayload>): Observable<Vendedor> {
    return this.http.put<Vendedor>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  getComisionDetalle(id: number): Observable<VendedorComisionDetalle> {
    return this.http.get<VendedorComisionDetalle>(`${this.baseUrl}/${id}/comision-detalle`);
  }

  getComisionDetallePdf(id: number): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/${id}/comision-detalle/pdf`, { responseType: 'blob' });
  }
}
