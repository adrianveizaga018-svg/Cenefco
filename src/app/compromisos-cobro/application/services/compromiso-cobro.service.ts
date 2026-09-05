import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CompromisoCobro,
  CompromisoCobroListParams,
  CompromisoCobroListResponse,
  CompromisoCobroLog,
  CreateCompromisoCobroPayload,
  ObservacionCompromisoCobroPayload,
  ReprogramarCompromisoCobroPayload,
  ResumenCompromisosCobro,
} from '../../domain/models/compromiso-cobro.model';

@Injectable({ providedIn: 'root' })
export class CompromisoCobroService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/compromisos-cobro';

  getAll(params: CompromisoCobroListParams): Observable<CompromisoCobroListResponse> {
    let httpParams = new HttpParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        httpParams = httpParams.set(key, String(value));
      }
    });
    return this.http.get<CompromisoCobroListResponse>(this.base, { params: httpParams });
  }

  getResumen(): Observable<ResumenCompromisosCobro> {
    return this.http.get<ResumenCompromisosCobro>(`${this.base}/resumen`);
  }

  getHistorial(id: number): Observable<CompromisoCobroLog[]> {
    return this.http.get<CompromisoCobroLog[]>(`${this.base}/${id}/historial`);
  }

  create(payload: CreateCompromisoCobroPayload): Observable<CompromisoCobro> {
    return this.http.post<CompromisoCobro>(this.base, payload);
  }

  reprogramar(id: number, payload: ReprogramarCompromisoCobroPayload): Observable<CompromisoCobro> {
    return this.http.patch<CompromisoCobro>(`${this.base}/${id}/reprogramar`, payload);
  }

  cumplir(id: number, payload: ObservacionCompromisoCobroPayload = {}): Observable<CompromisoCobro> {
    return this.http.patch<CompromisoCobro>(`${this.base}/${id}/cumplir`, payload);
  }

  cancelar(id: number, payload: ObservacionCompromisoCobroPayload = {}): Observable<CompromisoCobro> {
    return this.http.patch<CompromisoCobro>(`${this.base}/${id}/cancelar`, payload);
  }
}
