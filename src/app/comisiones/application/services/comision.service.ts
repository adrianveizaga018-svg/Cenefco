import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  ComisionLiquidacion,
  ComisionListParams,
  ComisionListResponse,
  ComisionSugerida,
  CrearComisionPayload,
} from '../../domain/models/comision.model';

@Injectable({ providedIn: 'root' })
export class ComisionService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/comisiones';

  getAll(p: ComisionListParams = {}): Observable<ComisionListResponse> {
    let params = new HttpParams();
    if (p.pageIndex   != null) params = params.set('pageIndex',   p.pageIndex);
    if (p.pageSize    != null) params = params.set('pageSize',    p.pageSize);
    if (p.vendedor_id != null) params = params.set('vendedor_id', p.vendedor_id);
    if (p.estado)               params = params.set('estado',      p.estado);
    return this.http.get<ComisionListResponse>(this.baseUrl, { params });
  }

  getById(id: number): Observable<ComisionLiquidacion> {
    return this.http.get<ComisionLiquidacion>(`${this.baseUrl}/${id}`);
  }

  getSugerida(vendedorId: number, fechaDesde: string, fechaHasta: string): Observable<ComisionSugerida> {
    const params = new HttpParams()
      .set('vendedor_id', vendedorId)
      .set('fecha_desde', fechaDesde)
      .set('fecha_hasta', fechaHasta);
    return this.http.get<ComisionSugerida>(`${this.baseUrl}/sugerida`, { params });
  }

  crear(payload: CrearComisionPayload): Observable<ComisionLiquidacion> {
    return this.http.post<ComisionLiquidacion>(this.baseUrl, payload);
  }

  aprobar(id: number): Observable<ComisionLiquidacion> {
    return this.http.patch<ComisionLiquidacion>(`${this.baseUrl}/${id}/aprobar`, {});
  }

  pagar(id: number, comprobante: File): Observable<ComisionLiquidacion> {

    const fd = new FormData();
    fd.append('comprobante_pago', comprobante);
    fd.append('_method', 'PATCH');
    return this.http.post<ComisionLiquidacion>(`${this.baseUrl}/${id}/pagar`, fd);
  }

  anular(id: number, nota?: string): Observable<ComisionLiquidacion> {
    return this.http.patch<ComisionLiquidacion>(`${this.baseUrl}/${id}/anular`, { nota: nota ?? null });
  }
}
