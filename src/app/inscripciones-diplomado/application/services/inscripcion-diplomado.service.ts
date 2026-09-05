import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  ConvertirInscripcionDiplomadoPayload,
  ConvertirInscripcionDiplomadoResult,
  InscripcionDiplomado,
  InscripcionDiplomadoListParams,
  InscripcionDiplomadoListResponse,
  UpdateInscripcionDiplomadoPayload,
} from '../../domain/models/inscripcion-diplomado.model';

@Injectable({ providedIn: 'root' })
export class InscripcionDiplomadoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/inscripciones-diplomado';

  getAll(params: InscripcionDiplomadoListParams = {}): Observable<InscripcionDiplomadoListResponse> {
    let p = new HttpParams();
    if (params.pageIndex   != null) p = p.set('pageIndex',   params.pageIndex);
    if (params.pageSize    != null) p = p.set('pageSize',    params.pageSize);
    if (params.query)               p = p.set('query',       params.query);
    if (params.estado)              p = p.set('estado',      params.estado);
    if (params.programa_id != null) p = p.set('programa_id', params.programa_id);
    return this.http.get<InscripcionDiplomadoListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<InscripcionDiplomado> {
    return this.http.get<InscripcionDiplomado>(`${this.baseUrl}/${id}`);
  }

  update(id: number, data: UpdateInscripcionDiplomadoPayload): Observable<InscripcionDiplomado> {
    return this.http.put<InscripcionDiplomado>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  convertirInscripcion(id: number, payload: ConvertirInscripcionDiplomadoPayload = {}): Observable<ConvertirInscripcionDiplomadoResult> {
    return this.http.post<ConvertirInscripcionDiplomadoResult>(`${this.baseUrl}/${id}/convertir-inscripcion`, payload);
  }
}
