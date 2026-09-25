import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface CobranzaFiltros {
  page?:        number;
  per_page?:    number;
  estado?:      string;
  ci?:          string;
  programa_id?: number | null;
  id_vendedor?: number | null;
  metodo_pago?: string;
  canal_venta?: string;
  fecha_desde?: string;
  fecha_hasta?: string;
}

@Injectable({ providedIn: 'root' })
export class CobranzaService {
  private http = inject(HttpClient);

  getMetrics(): Observable<any> {
    return this.http.get('/api/v1/cobranzas/metrics');
  }

  getDashboard(params: CobranzaFiltros): Observable<any> {
    let p = new HttpParams();
    if (params.page)        p = p.set('page',        params.page);
    if (params.per_page)    p = p.set('per_page',    params.per_page);
    if (params.estado)      p = p.set('estado',      params.estado);
    if (params.ci?.trim())  p = p.set('ci',          params.ci.trim());
    if (params.programa_id) p = p.set('programa_id', params.programa_id);
    if (params.id_vendedor) p = p.set('id_vendedor', params.id_vendedor);
    if (params.metodo_pago) p = p.set('metodo_pago', params.metodo_pago);
    if (params.canal_venta) p = p.set('canal_venta', params.canal_venta);
    if (params.fecha_desde) p = p.set('fecha_desde', params.fecha_desde);
    if (params.fecha_hasta) p = p.set('fecha_hasta', params.fecha_hasta);
    return this.http.get('/api/v1/cobranzas', { params: p });
  }

  getCuotasInscripcion(idIns: number): Observable<any[]> {
    return this.http.get<any[]>(`/api/v1/cobranzas/inscripcion/${idIns}/cuotas`);
  }

  getResumen(params: Omit<CobranzaFiltros, 'page' | 'per_page' | 'estado'>): Observable<any> {
    let p = new HttpParams();
    if (params.ci?.trim())  p = p.set('ci',          params.ci.trim());
    if (params.programa_id) p = p.set('programa_id', params.programa_id);
    if (params.id_vendedor) p = p.set('id_vendedor', params.id_vendedor);
    if (params.canal_venta) p = p.set('canal_venta', params.canal_venta);
    return this.http.get('/api/v1/cobranzas/resumen', { params: p });
  }
}
