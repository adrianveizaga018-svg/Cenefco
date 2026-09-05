import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { IngresoListParams, IngresoListResponse, IngresoResumen } from '../../domain/models/ingreso.model';

@Injectable({ providedIn: 'root' })
export class IngresoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/ingresos';

  getAll(params: IngresoListParams = {}): Observable<IngresoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.desde)             p = p.set('desde',     params.desde);
    if (params.hasta)             p = p.set('hasta',     params.hasta);
    return this.http.get<IngresoListResponse>(this.baseUrl, { params: p });
  }

  getResumen(): Observable<IngresoResumen> {
    return this.http.get<IngresoResumen>(`${this.baseUrl}/resumen`);
  }
}
