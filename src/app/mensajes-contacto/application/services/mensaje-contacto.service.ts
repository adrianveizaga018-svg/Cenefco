import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  MensajeContacto,
  MensajeContactoListParams,
  MensajeContactoListResponse,
  ResponderMensajePayload,
} from '../../domain/models/mensaje-contacto.model';

@Injectable({ providedIn: 'root' })
export class MensajeContactoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/mensajes-contacto';

  getAll(params: MensajeContactoListParams = {}): Observable<MensajeContactoListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return this.http.get<MensajeContactoListResponse>(this.baseUrl, { params: httpParams });
  }

  getById(id: number): Observable<MensajeContacto> {
    return this.http.get<MensajeContacto>(`${this.baseUrl}/${id}`);
  }

  responder(id: number, data: ResponderMensajePayload): Observable<MensajeContacto> {
    return this.http.post<MensajeContacto>(`${this.baseUrl}/${id}/responder`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
