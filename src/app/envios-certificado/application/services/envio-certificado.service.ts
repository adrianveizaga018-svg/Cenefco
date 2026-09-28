import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  EnvioCertificado,
  CreateEnvioPayload,
  UpdateEnvioPayload,
  EnvioDashboardFiltros,
} from '../../domain/models/envio-certificado.model';

@Injectable({ providedIn: 'root' })
export class EnvioCertificadoService {
  private http = inject(HttpClient);
  private readonly base = '/api/v1/envios-certificado';

  /** Envíos de una inscripción específica (usado en inscripcion-detail) */
  getByInscripcion(idIns: number): Observable<EnvioCertificado[]> {
    return this.http.get<EnvioCertificado[]>(this.base, { params: { id_ins: idIns } });
  }

  /** Dashboard general con filtros */
  getDashboard(filtros: EnvioDashboardFiltros = {}): Observable<any> {
    let p = new HttpParams();
    if (filtros.estado)       p = p.set('estado',       filtros.estado);
    if (filtros.departamento) p = p.set('departamento', filtros.departamento);
    if (filtros.ci?.trim())   p = p.set('ci',           filtros.ci.trim());
    if (filtros.fecha_desde)  p = p.set('fecha_desde',  filtros.fecha_desde);
    if (filtros.fecha_hasta)  p = p.set('fecha_hasta',  filtros.fecha_hasta);
    if (filtros.page)         p = p.set('page',         filtros.page);
    if (filtros.per_page)     p = p.set('per_page',     filtros.per_page ?? 25);
    return this.http.get<any>(`${this.base}/dashboard`, { params: p });
  }

  /** Autocomplete de ciudades ya registradas */
  autocomplete(query: string, departamento?: string): Observable<string[]> {
    let p = new HttpParams().set('q', query);
    if (departamento) p = p.set('departamento', departamento);
    return this.http.get<string[]>(`${this.base}/autocomplete`, { params: p });
  }

  /** Crear un nuevo envío */
  create(payload: CreateEnvioPayload): Observable<EnvioCertificado> {
    const fd = new FormData();
    fd.append('id_ins',         String(payload.id_ins));
    if (payload.departamento)   fd.append('departamento',   payload.departamento);
    fd.append('ciudad_destino', payload.ciudad_destino);
    fd.append('fecha_envio',    payload.fecha_envio);
    if (payload.imagen_guia)    fd.append('imagen_guia', payload.imagen_guia);
    if (payload.aclaraciones)   fd.append('aclaraciones', payload.aclaraciones);
    if (payload.costo != null)  fd.append('costo', String(payload.costo));
    return this.http.post<EnvioCertificado>(this.base, fd);
  }

  /** Actualizar estado del envío (marcar enviado, agregar agencia, etc.) */
  update(id: number, payload: UpdateEnvioPayload): Observable<EnvioCertificado> {
    return this.http.put<EnvioCertificado>(`${this.base}/${id}`, payload);
  }

  /** Eliminar */
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/${id}`);
  }
}
