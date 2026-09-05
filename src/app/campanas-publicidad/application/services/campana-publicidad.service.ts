import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CampanaPublicidad,
  CampanaPublicidadListResponse,
  CreateCampanaPublicidadPayload,
  UpdateCampanaPublicidadPayload,
  RegistrarMetricaPayload,
  CampanaMetrica,
  ReporteCampana,
} from '../../domain/models/campana-publicidad.model';

@Injectable({ providedIn: 'root' })
export class CampanaPublicidadService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/campanas-publicidad';

  getAll(params: {
    pageIndex?: number;
    pageSize?: number;
    query?: string;
    sortKey?: string;
    sortOrder?: string;
    programa_id?: number;
    plataforma?: string;
    estado?: string;
    fecha_desde?: string;
    fecha_hasta?: string;
  }): Observable<CampanaPublicidadListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<CampanaPublicidadListResponse>(this.base, { params: httpParams });
  }

  getById(id: number): Observable<CampanaPublicidad> {
    return this.http.get<CampanaPublicidad>(`${this.base}/${id}`);
  }

  create(payload: CreateCampanaPublicidadPayload): Observable<CampanaPublicidad> {
    return this.http.post<CampanaPublicidad>(this.base, payload);
  }

  update(id: number, payload: UpdateCampanaPublicidadPayload): Observable<CampanaPublicidad> {
    return this.http.put<CampanaPublicidad>(`${this.base}/${id}`, payload);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  registrarMetrica(id: number, payload: RegistrarMetricaPayload): Observable<CampanaMetrica> {
    return this.http.post<CampanaMetrica>(`${this.base}/${id}/metricas`, payload);
  }

  getReporte(fechaDesde?: string, fechaHasta?: string): Observable<ReporteCampana[]> {
    let httpParams = new HttpParams();
    if (fechaDesde) httpParams = httpParams.set('fecha_desde', fechaDesde);
    if (fechaHasta) httpParams = httpParams.set('fecha_hasta', fechaHasta);
    return this.http.get<ReporteCampana[]>(`${this.base}/reporte`, { params: httpParams });
  }
}
