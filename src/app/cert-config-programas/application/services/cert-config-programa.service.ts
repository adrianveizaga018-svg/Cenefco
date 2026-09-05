import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CertConfigPrograma,
  CertConfigItem,
  CertConfigItemPayload,
  CertSolicitud,
  CertSolicitudListResponse,
  UpsertCertConfigPayload,
} from '../../domain/models/cert-config-programa.model';

@Injectable({ providedIn: 'root' })
export class CertConfigProgramaService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/cert-config-programas';
  private readonly solBase = '/api/v1/cert-solicitudes';

  getAll(): Observable<{ data: CertConfigPrograma[]; total: number }> {
    return this.http.get<{ data: CertConfigPrograma[]; total: number }>(this.base);
  }

  getByPrograma(programaId: number): Observable<CertConfigPrograma> {
    return this.http.get<CertConfigPrograma>(`${this.base}/by-programa/${programaId}`);
  }

  upsert(payload: UpsertCertConfigPayload): Observable<CertConfigPrograma> {
    return this.http.post<CertConfigPrograma>(this.base, payload);
  }

  toggle(id: number): Observable<CertConfigPrograma> {
    return this.http.patch<CertConfigPrograma>(`${this.base}/${id}/toggle`, {});
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  createItem(configId: number, payload: CertConfigItemPayload): Observable<CertConfigItem> {
    return this.http.post<CertConfigItem>(`${this.base}/${configId}/items`, payload);
  }

  updateItem(configId: number, itemId: number, payload: CertConfigItemPayload): Observable<CertConfigItem> {
    return this.http.put<CertConfigItem>(`${this.base}/${configId}/items/${itemId}`, payload);
  }

  deleteItem(configId: number, itemId: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${configId}/items/${itemId}`);
  }

  getSolicitudes(params: { pageIndex?: number; pageSize?: number; estado?: string; programa_id?: number }): Observable<CertSolicitudListResponse> {
    return this.http.get<CertSolicitudListResponse>(this.solBase, { params: params as any });
  }

  aprobar(id: number): Observable<CertSolicitud> {
    return this.http.patch<CertSolicitud>(`${this.solBase}/${id}/aprobar`, {});
  }

  rechazar(id: number, nota_admin: string): Observable<CertSolicitud> {
    return this.http.patch<CertSolicitud>(`${this.solBase}/${id}/rechazar`, { nota_admin });
  }

  getPendientesCount(): Observable<{ pendientes: number }> {
    return this.http.get<{ pendientes: number }>(`${this.solBase}/pendientes-count`);
  }
}
