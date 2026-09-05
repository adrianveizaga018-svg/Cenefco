import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CertVerificacion, CertVerificacionListResponse } from '../../domain/models/cert-verificacion.model';

export interface CertVerificacionListParams {
  pageIndex?: number;
  pageSize?: number;
  certificado_id?: number;
  resultado?: string;
}

@Injectable({ providedIn: 'root' })
export class CertVerificacionService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/cert-verificaciones';

  getAll(p: CertVerificacionListParams = {}): Observable<CertVerificacionListResponse> {
    let params = new HttpParams();
    if (p.pageIndex)      params = params.set('pageIndex',      p.pageIndex);
    if (p.pageSize)       params = params.set('pageSize',       p.pageSize);
    if (p.certificado_id) params = params.set('certificado_id', p.certificado_id);
    if (p.resultado)      params = params.set('resultado',      p.resultado);
    return this.http.get<CertVerificacionListResponse>(this.baseUrl, { params });
  }

  getById(id: number): Observable<CertVerificacion> {
    return this.http.get<CertVerificacion>(`${this.baseUrl}/${id}`);
  }
}
