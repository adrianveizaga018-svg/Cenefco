import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  VentaDetalle,
  VentaListParams,
  VentaListResponse,
  VentaReporteResponse,
  RegistrarPagoVentaPayload,
} from '../../domain/models/venta.model';
import { ReglamentoPrograma } from '../../domain/models/reglamento.model';

@Injectable({ providedIn: 'root' })
export class VentaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/ventas';

  getAll(params: VentaListParams = {}): Observable<VentaListResponse> {
    let p = new HttpParams();
    if (params.pageIndex    != null) p = p.set('pageIndex',   params.pageIndex);
    if (params.pageSize     != null) p = p.set('pageSize',    params.pageSize);
    if (params.query)                p = p.set('query',       params.query);
    if (params.estado_pago)          p = p.set('estado_pago', params.estado_pago);
    if (params.periodo)              p = p.set('periodo',     params.periodo);
    if (params.gestion      != null) p = p.set('gestion',     params.gestion);
    if (params.conInactivos)         p = p.set('conInactivos', '1');
    if (params.id_vendedor  != null) p = p.set('id_vendedor', params.id_vendedor);
    if (params.canal_venta)          p = p.set('canal_venta', params.canal_venta);
    return this.http.get<VentaListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<VentaDetalle> {
    return this.http.get<VentaDetalle>(`${this.baseUrl}/${id}`);
  }

  getReporte(params: Omit<VentaListParams, 'pageIndex' | 'pageSize'> = {}): Observable<VentaReporteResponse> {
    let p = new HttpParams();
    if (params.query)            p = p.set('query',       params.query);
    if (params.periodo)          p = p.set('periodo',     params.periodo);
    if (params.gestion != null)  p = p.set('gestion',     params.gestion);
    if (params.id_vendedor != null) p = p.set('id_vendedor', params.id_vendedor);
    return this.http.get<VentaReporteResponse>(`${this.baseUrl}/reporte`, { params: p });
  }

  getReportePorVendedor(params: { gestion?: number; periodo?: string } = {}): Observable<{ data: any[] }> {
    let p = new HttpParams();
    if (params.gestion != null) p = p.set('gestion', params.gestion);
    if (params.periodo)         p = p.set('periodo', params.periodo);
    return this.http.get<{ data: any[] }>(`${this.baseUrl}/reporte/por-vendedor`, { params: p });
  }

  getReportePorCanal(params: { gestion?: number; periodo?: string } = {}): Observable<{ data: any[] }> {
    let p = new HttpParams();
    if (params.gestion != null) p = p.set('gestion', params.gestion);
    if (params.periodo)         p = p.set('periodo', params.periodo);
    return this.http.get<{ data: any[] }>(`${this.baseUrl}/reporte/por-canal`, { params: p });
  }

  getProyeccionCobros(meses = 6): Observable<{ data: any[]; meses: number }> {
    return this.http.get<{ data: any[]; meses: number }>(`${this.baseUrl}/reporte/proyeccion`, {
      params: new HttpParams().set('meses', meses),
    });
  }

  registrarPago(payload: RegistrarPagoVentaPayload): Observable<any> {
    return this.http.post<any>('/api/v1/pagos-academicos', payload);
  }

  descargarPdf(id: number): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/${id}/pdf`, { responseType: 'blob' });
  }

  getReglamento(idPrograma: number): Observable<ReglamentoPrograma> {
    return this.http.get<ReglamentoPrograma>(`/api/v1/reglamentos/${idPrograma}`);
  }

  saveReglamento(idPrograma: number, data: Partial<ReglamentoPrograma>): Observable<ReglamentoPrograma> {
    return this.http.put<ReglamentoPrograma>(`/api/v1/reglamentos/${idPrograma}`, data);
  }
}
