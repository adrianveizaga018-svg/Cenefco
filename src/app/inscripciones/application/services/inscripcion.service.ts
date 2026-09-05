import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Inscripcion, InscripcionDetalle,
  InscripcionListResponse, InscripcionListParams,
  CreateInscripcionPayload, DocumentosInscripcion, DevolucionItem,
} from '../../domain/models/inscripcion.model';
import { ReglamentoPrograma } from '../../../ventas/domain/models/reglamento.model';

@Injectable({ providedIn: 'root' })
export class InscripcionService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/inscripciones';

  getAll(params: InscripcionListParams = {}): Observable<InscripcionListResponse> {
    let p = new HttpParams();
    if (params.pageIndex  != null) p = p.set('pageIndex',    params.pageIndex);
    if (params.pageSize   != null) p = p.set('pageSize',     params.pageSize);
    if (params.query)              p = p.set('query',         params.query);
    if (params.id_us       != null) p = p.set('id_us',        params.id_us);
    if (params.id_imp      != null) p = p.set('id_imp',       params.id_imp);
    if (params.programa_id != null) p = p.set('programa_id',  params.programa_id);
    if (params.periodo)             p = p.set('periodo',       params.periodo);
    if (params.gestion)            p = p.set('gestion',       params.gestion);
    if (params.conInactivos)       p = p.set('conInactivos', 'true');
    return this.http.get<InscripcionListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<Inscripcion> {
    return this.http.get<Inscripcion>(`${this.baseUrl}/${id}`);
  }

  getDetalle(id: number): Observable<InscripcionDetalle> {
    return this.http.get<InscripcionDetalle>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateInscripcionPayload): Observable<Inscripcion> {
    return this.http.post<Inscripcion>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateInscripcionPayload>): Observable<Inscripcion> {
    return this.http.put<Inscripcion>(`${this.baseUrl}/${id}`, data);
  }

  getDocumentos(id: number): Observable<DocumentosInscripcion> {
    return this.http.get<DocumentosInscripcion>(`${this.baseUrl}/${id}/documentos`);
  }

  registrarDocumento(inscripcionId: number, payload: {
    id_fechadoc: number;
    dejo_documento_fisico?: boolean;
    documento_digital?: string | null;
    observacion_doc?: string | null;
  }): Observable<any> {
    return this.http.post(`${this.baseUrl}/${inscripcionId}/documentos`, payload);
  }

  uploadDocumentoFile(file: File): Observable<{ url: string }> {
    const fd = new FormData();
    fd.append('file', file);
    return this.http.post<{ url: string }>('/api/v1/upload/file', fd);
  }

  registrarPago(payload: {
    id_ins:               number;
    id_us:                number;
    id_fechapago?:        number | null;
    monto_pagado:         number;
    metodo_pago:          string;
    id_us_cajero?:        number | null;
    nro_boleta_bancaria?: string | null;
    tipo_banco_id?:       number | null;
    fecha_deposito?:      string | null;
    observacion_pago?:    string | null;
    comprobante_archivo?: File | null;
    monto_descuento?:     number | null;
    motivo_descuento?:    string | null;
  }): Observable<any> {
    const fd = new FormData();
    Object.entries(payload).forEach(([k, v]) => {
      if (k === 'comprobante_archivo') return;
      if (v != null) fd.append(k, String(v));
    });
    if (payload.comprobante_archivo) fd.append('comprobante_archivo', payload.comprobante_archivo);
    return this.http.post<any>('/api/v1/pagos-academicos', fd);
  }

  editarPago(idPago: number, payload: {
    monto_pagado?:        number;
    nro_boleta_bancaria?: string | null;
    tipo_banco_id?:       number | null;
    fecha_deposito?:      string | null;
    observacion_pago?:    string | null;
    comprobante_archivo?: File | null;
    monto_descuento?:     number | null;
    motivo_descuento?:    string | null;
  }): Observable<any> {
    if (!payload.comprobante_archivo) {
      return this.http.put<any>(`/api/v1/pagos-academicos/${idPago}`, payload);
    }



    const fd = new FormData();
    Object.entries(payload).forEach(([k, v]) => {
      if (k === 'comprobante_archivo') return;
      if (v != null) fd.append(k, String(v));
    });
    fd.append('comprobante_archivo', payload.comprobante_archivo);
    fd.append('_method', 'PUT');
    return this.http.post<any>(`/api/v1/pagos-academicos/${idPago}`, fd);
  }

  verificarPago(idPago: number): Observable<any> {
    return this.http.patch<any>(`/api/v1/pagos-academicos/${idPago}/verificar`, {});
  }

  observarPago(idPago: number, notaVerificacion: string): Observable<any> {
    return this.http.patch<any>(`/api/v1/pagos-academicos/${idPago}/observar`, { nota_verificacion: notaVerificacion });
  }

  registrarAnticipo(idIns: number, payload: {
    monto_pagado:          number;
    nro_boleta_bancaria?:  string;
    fecha_deposito?:       string;
    observacion_pago?:     string;
    metodo_pago?:          string;
    id_us_cajero?:         number | null;
  }): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/${idIns}/anticipo`, payload);
  }

  getDevoluciones(idIns: number): Observable<DevolucionItem[]> {
    return this.http.get<DevolucionItem[]>(`${this.baseUrl}/${idIns}/devoluciones`);
  }

  crearDevolucion(idIns: number, data: {
    monto: number;
    motivo: string;
    documento_url?: string | null;
  }): Observable<DevolucionItem> {
    return this.http.post<DevolucionItem>(`${this.baseUrl}/${idIns}/devoluciones`, data);
  }

  cancelarDevolucion(idDev: number): Observable<DevolucionItem> {
    return this.http.patch<DevolucionItem>(`/api/v1/devoluciones/${idDev}/cancelar`, { estado: 'cancelada' });
  }

  resolverDevolucion(idDev: number, estado: 'aprobada' | 'rechazada', nota?: string): Observable<DevolucionItem> {
    return this.http.patch<DevolucionItem>(`/api/v1/devoluciones/${idDev}/resolver`, { estado, nota_respuesta: nota ?? null });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  marcarParticipante(idIns: number): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/${idIns}/marcar-participante`, {});
  }

  marcarParticipantesBulk(ids: number[]): Observable<{ registrados: any[]; omitidos: { id_ins: number; motivo: string }[] }> {
    return this.http.post<{ registrados: any[]; omitidos: { id_ins: number; motivo: string }[] }>(
      `${this.baseUrl}/marcar-participantes`,
      { ids }
    );
  }

  descargarPdf(id: number): Observable<Blob> {
    return this.http.get(`/api/v1/ventas/${id}/pdf`, { responseType: 'blob' });
  }

  enviarComprobantePorCorreo(id: number, email?: string): Observable<{ message: string; email: string }> {
    return this.http.post<{ message: string; email: string }>(
      `/api/v1/ventas/${id}/enviar-correo`,
      email ? { email } : {}
    );
  }

  getReglamento(idPrograma: number): Observable<ReglamentoPrograma> {
    return this.http.get<ReglamentoPrograma>(`/api/v1/reglamentos/${idPrograma}`);
  }

  saveReglamento(idPrograma: number, data: Partial<ReglamentoPrograma>): Observable<ReglamentoPrograma> {
    return this.http.put<ReglamentoPrograma>(`/api/v1/reglamentos/${idPrograma}`, data);
  }
}
