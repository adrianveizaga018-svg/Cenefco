import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { FechaPago, FechaPagoListParams, FechaPagoListResponse, CreateFechaPagoPayload } from '../../domain/models/fecha-pago.model';

@Injectable({ providedIn: 'root' })
export class FechaPagoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/fechas-pago';
  getAll(params: FechaPagoListParams = {}): Observable<FechaPagoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex    != null) p = p.set('pageIndex',    params.pageIndex);
    if (params.pageSize     != null) p = p.set('pageSize',     params.pageSize);
    if (params.id_plan      != null) p = p.set('id_plan',      params.id_plan);
    p = p.set('conInactivos', params.conInactivos ? 'true' : 'false');
    return this.http.get<FechaPagoListResponse>(this.baseUrl, { params: p });
  }
  getById(id: number): Observable<FechaPago> { return this.http.get<FechaPago>(`${this.baseUrl}/${id}`); }
  create(data: CreateFechaPagoPayload): Observable<FechaPago> { return this.http.post<FechaPago>(this.baseUrl, data); }
  update(id: number, data: Partial<CreateFechaPagoPayload>): Observable<FechaPago> { return this.http.put<FechaPago>(`${this.baseUrl}/${id}`, data); }
  delete(id: number): Observable<void> { return this.http.delete<void>(`${this.baseUrl}/${id}`); }

  generarLote(payload: {
    id_plan:       number;
    nro_cuotas:    number;
    monto_total:   number;
    fecha_inicio:  string;
    intervalo:     'mensual' | 'quincenal' | 'semanal';
    tipo_tramite:  string;
  }): Observable<{ cuotas_generadas: number; ids: number[] }> {
    return this.http.post<{ cuotas_generadas: number; ids: number[] }>(
      `${this.baseUrl}/generar-lote`, payload
    );
  }
}
