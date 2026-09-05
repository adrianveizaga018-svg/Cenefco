import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CuentasResponse, CreateReunionPayload, CreateZoomCuentaPayload,
         CrearReunionResponse, GrabacionesResponse, ReunionesResponse, ZoomCuenta } from '../../domain/models/zoom.model';

@Injectable({ providedIn: 'root' })
export class ZoomService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/zoom';

  getCuentas(): Observable<CuentasResponse>        { return this.http.get<CuentasResponse>(`${this.base}/cuentas`); }
  createCuenta(d: CreateZoomCuentaPayload): Observable<ZoomCuenta> { return this.http.post<ZoomCuenta>(`${this.base}/cuentas`, d); }
  updateCuenta(id: number, d: Partial<CreateZoomCuentaPayload>): Observable<ZoomCuenta> { return this.http.put<ZoomCuenta>(`${this.base}/cuentas/${id}`, d); }
  deleteCuenta(id: number): Observable<void>       { return this.http.delete<void>(`${this.base}/cuentas/${id}`); }
  setPredeterminada(id: number): Observable<any>   { return this.http.post<any>(`${this.base}/cuentas/${id}/predeterminada`, {}); }
  testCuenta(id: number): Observable<any>          { return this.http.post<any>(`${this.base}/cuentas/${id}/test`, {}); }

  getReuniones(cuentaId?: number): Observable<ReunionesResponse> {
    const params = cuentaId ? new HttpParams().set('cuenta_id', cuentaId) : undefined;
    return this.http.get<ReunionesResponse>(`${this.base}/meetings`, { params });
  }

  crearReunion(payload: CreateReunionPayload): Observable<CrearReunionResponse> {
    return this.http.post<CrearReunionResponse>(`${this.base}/meetings`, payload);
  }

  getGrabaciones(cuentaId?: number): Observable<GrabacionesResponse> {
    const params = cuentaId ? new HttpParams().set('cuenta_id', cuentaId) : undefined;
    return this.http.get<GrabacionesResponse>(`${this.base}/recordings`, { params });
  }
}
