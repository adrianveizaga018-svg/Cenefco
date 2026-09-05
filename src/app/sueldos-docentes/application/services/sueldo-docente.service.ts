import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  SueldoDocente,
  SueldoDocenteDetalle,
  SueldoDocenteListResponse,
  SueldoDocenteParams,
  DocenteOption,
  ImparticionOption,
  PagoSueldo,
} from '../../domain/models/sueldo-docente.model';

@Injectable({ providedIn: 'root' })
export class SueldoDocenteService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/sueldos-docentes';

  getAll(params: SueldoDocenteParams = {}): Observable<SueldoDocenteListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',   params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',    params.pageSize);
    if (params.query)               p = p.set('query',       params.query);
    if (params.periodo)             p = p.set('periodo',     params.periodo);
    if (params.gestion    != null)  p = p.set('gestion',     params.gestion);
    if (params.estado_pago)         p = p.set('estado_pago', params.estado_pago);
    return this.http.get<SueldoDocenteListResponse>(this.base, { params: p });
  }

  getById(id: number): Observable<SueldoDocenteDetalle> {
    return this.http.get<SueldoDocenteDetalle>(`${this.base}/${id}`);
  }

  create(data: Partial<SueldoDocente> & { archivo_pdf_file?: File | null }): Observable<SueldoDocente> {
    const fd = new FormData();
    Object.entries(data).forEach(([k, v]) => {
      if (k === 'archivo_pdf_file') return;
      if (v != null) fd.append(k, String(v));
    });
    if (data.archivo_pdf_file) fd.append('archivo_pdf', data.archivo_pdf_file);
    return this.http.post<SueldoDocente>(this.base, fd);
  }

  update(id: number, data: Partial<SueldoDocente> & { archivo_pdf_file?: File | null }): Observable<SueldoDocente> {
    const fd = new FormData();
    fd.append('_method', 'PUT');
    Object.entries(data).forEach(([k, v]) => {
      if (k === 'archivo_pdf_file') return;
      if (v != null) fd.append(k, String(v));
    });
    if (data.archivo_pdf_file) fd.append('archivo_pdf', data.archivo_pdf_file);
    return this.http.post<SueldoDocente>(`${this.base}/${id}`, fd);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }

  addPago(idSueldo: number, data: {
    monto_pagado:    number | null;
    fecha_pago:      string;
    nro_comprobante: string;
    observacion:     string;
    archivo?:        File | null;
  }): Observable<PagoSueldo> {
    const fd = new FormData();
    if (data.monto_pagado != null) fd.append('monto_pagado',    String(data.monto_pagado));
    if (data.fecha_pago)           fd.append('fecha_pago',      data.fecha_pago);
    if (data.nro_comprobante)      fd.append('nro_comprobante', data.nro_comprobante);
    if (data.observacion)          fd.append('observacion',     data.observacion);
    if (data.archivo)              fd.append('comprobante_archivo', data.archivo);
    return this.http.post<PagoSueldo>(`${this.base}/${idSueldo}/pagos`, fd);
  }

  addPagoLote(idSueldo: number, cuotas: Array<{
    monto_pagado:    number;
    fecha_pago:      string;
    nro_comprobante: string;
    observacion:     string;
  }>): Observable<PagoSueldo[]> {
    return this.http.post<PagoSueldo[]>(`${this.base}/${idSueldo}/pagos/lote`, { cuotas });
  }

  deletePago(idSueldo: number, idPago: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${idSueldo}/pagos/${idPago}`);
  }

  getDocentes(): Observable<DocenteOption[]> {
    return this.http.get<DocenteOption[]>(`${this.base}/docentes`);
  }

  getImparticiones(): Observable<ImparticionOption[]> {
    return this.http.get<ImparticionOption[]>(`${this.base}/imparticiones`);
  }
}
