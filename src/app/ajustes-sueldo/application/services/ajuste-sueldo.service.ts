import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AjusteSueldo, AjusteSueldoListResponse, CreateAjusteSueldoPayload } from '../../domain/models/ajuste-sueldo.model';

export interface AjusteSueldoListParams {
  pageIndex?: number;
  pageSize?: number;
  empleado_id?: number;
  anio?: number;
  mes?: number;
  aplicado?: boolean;
}

@Injectable({ providedIn: 'root' })
export class AjusteSueldoService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/ajustes-sueldo';

  getAll(p: AjusteSueldoListParams = {}): Observable<AjusteSueldoListResponse> {
    let params = new HttpParams();
    if (p.pageIndex)             params = params.set('pageIndex', p.pageIndex);
    if (p.pageSize)              params = params.set('pageSize', p.pageSize);
    if (p.empleado_id)           params = params.set('empleado_id', p.empleado_id);
    if (p.anio)                  params = params.set('anio', p.anio);
    if (p.mes)                   params = params.set('mes', p.mes);
    if (p.aplicado !== undefined) params = params.set('aplicado', p.aplicado ? '1' : '0');
    return this.http.get<AjusteSueldoListResponse>(this.base, { params });
  }

  create(payload: CreateAjusteSueldoPayload): Observable<AjusteSueldo> {
    return this.http.post<AjusteSueldo>(this.base, payload);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
