import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  CreateCursoMigradoPayload,
  CursoMigrado,
  CursoMigradoListParams,
  CursoMigradoListResponse,
  CursoMigradoStats,
  ImportarExcelResult,
  ImportarJsonResult,
  LogoMigrado,
  ParticipanteBusquedaListResponse,
  ParticipanteMigrado,
  UpdateCursoMigradoPayload,
} from '../../domain/models/curso-migrado.model';

@Injectable({ providedIn: 'root' })
export class CursoMigradoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/cursos-migrados';

  create(payload: CreateCursoMigradoPayload): Observable<CursoMigrado> {
    return this.http.post<CursoMigrado>(this.baseUrl, payload);
  }

  getAll(params: CursoMigradoListParams = {}): Observable<CursoMigradoListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    if (params.mes)               httpParams = httpParams.set('mes',       params.mes);
    if (params.participantesMin != null) httpParams = httpParams.set('participantesMin', params.participantesMin);
    if (params.participantesMax != null) httpParams = httpParams.set('participantesMax', params.participantesMax);
    return this.http.get<CursoMigradoListResponse>(this.baseUrl, { params: httpParams });
  }

  getById(id: number): Observable<CursoMigrado> {
    return this.http.get<CursoMigrado>(`${this.baseUrl}/${id}`);
  }

  getStats(): Observable<CursoMigradoStats> {
    return this.http.get<CursoMigradoStats>(`${this.baseUrl}/stats`);
  }

  buscarParticipantes(params: CursoMigradoListParams = {}): Observable<ParticipanteBusquedaListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return this.http.get<ParticipanteBusquedaListResponse>(`${this.baseUrl}/participantes/buscar`, { params: httpParams });
  }

  addParticipante(id: number, nombre_completo: string): Observable<ParticipanteMigrado> {
    return this.http.post<ParticipanteMigrado>(`${this.baseUrl}/${id}/participantes`, { nombre_completo });
  }

  updateParticipante(id: number, participanteId: number, nombre_completo: string): Observable<ParticipanteMigrado> {
    return this.http.put<ParticipanteMigrado>(`${this.baseUrl}/${id}/participantes/${participanteId}`, { nombre_completo });
  }

  deleteParticipante(id: number, participanteId: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}/participantes/${participanteId}`);
  }

  exportPdf(id: number): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/${id}/export-pdf`, { responseType: 'blob' });
  }

  exportExcel(id: number): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/${id}/export-excel`, { responseType: 'blob' });
  }

  exportAllPdf(): Observable<Blob> {
    return this.http.get('/api/v1/cursos-migrados-export/pdf', { responseType: 'blob' });
  }

  exportAllExcel(): Observable<Blob> {
    return this.http.get('/api/v1/cursos-migrados-export/excel', { responseType: 'blob' });
  }

  importarJson(archivo: File): Observable<ImportarJsonResult> {
    const form = new FormData();
    form.append('archivo', archivo);
    return this.http.post<ImportarJsonResult>(`${this.baseUrl}/importar-json`, form);
  }

  importarExcel(cursoId: number, archivo: File): Observable<ImportarExcelResult> {
    const form = new FormData();
    form.append('archivo', archivo);
    return this.http.post<ImportarExcelResult>(`${this.baseUrl}/${cursoId}/participantes/importar-excel`, form);
  }

  update(id: number, payload: UpdateCursoMigradoPayload): Observable<CursoMigrado> {
    return this.http.put<CursoMigrado>(`${this.baseUrl}/${id}`, payload);
  }

  uploadImagen(id: number, file: File): Observable<{ imagen_path: string }> {
    const form = new FormData();
    form.append('imagen', file);
    return this.http.post<{ imagen_path: string }>(`${this.baseUrl}/${id}/imagen`, form);
  }

  deleteImagen(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}/imagen`);
  }

  uploadLogo(id: number, file: File, nombre?: string): Observable<LogoMigrado> {
    const form = new FormData();
    form.append('logo', file);
    if (nombre) form.append('nombre', nombre);
    return this.http.post<LogoMigrado>(`${this.baseUrl}/${id}/logos`, form);
  }

  deleteLogo(id: number, logoId: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}/logos/${logoId}`);
  }

  generarQr(id: number): Observable<{ qr_path: string }> {
    return this.http.post<{ qr_path: string }>(`${this.baseUrl}/${id}/generar-qr`, {});
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
