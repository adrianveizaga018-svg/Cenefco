import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CorreoEnviadoListResponse } from '../../domain/models/correo-enviado.model';

export interface CorreoEnviadoListParams {
  pageIndex?: number;
  pageSize?: number;
  query?: string;
  referencia_tipo?: string;
  referencia_id?: number;
}

@Injectable({ providedIn: 'root' })
export class CorreoEnviadoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/correos-enviados';

  getAll(p: CorreoEnviadoListParams = {}): Observable<CorreoEnviadoListResponse> {
    let params = new HttpParams();
    if (p.pageIndex)       params = params.set('pageIndex',       p.pageIndex);
    if (p.pageSize)        params = params.set('pageSize',        p.pageSize);
    if (p.query)           params = params.set('query',           p.query);
    if (p.referencia_tipo) params = params.set('referencia_tipo', p.referencia_tipo);
    if (p.referencia_id)   params = params.set('referencia_id',   p.referencia_id);
    return this.http.get<CorreoEnviadoListResponse>(this.baseUrl, { params });
  }
}
