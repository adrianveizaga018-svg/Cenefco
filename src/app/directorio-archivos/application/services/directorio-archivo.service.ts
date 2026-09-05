import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  ArchivoParticipante,
  CursoDirectorioListResponse,
  DirectorioListParams,
  ParticipanteDirectorioListResponse,
} from '../../domain/models/directorio-archivo.model';

@Injectable({ providedIn: 'root' })
export class DirectorioArchivoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/directorio-archivos';

  private toHttpParams(params: DirectorioListParams): HttpParams {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return httpParams;
  }

  getCursos(params: DirectorioListParams = {}): Observable<CursoDirectorioListResponse> {
    return this.http.get<CursoDirectorioListResponse>(`${this.baseUrl}/cursos`, { params: this.toHttpParams(params) });
  }

  getParticipantes(idImp: number, params: DirectorioListParams = {}): Observable<ParticipanteDirectorioListResponse> {
    return this.http.get<ParticipanteDirectorioListResponse>(`${this.baseUrl}/cursos/${idImp}/participantes`, { params: this.toHttpParams(params) });
  }

  getArchivos(idIns: number): Observable<ArchivoParticipante> {
    return this.http.get<ArchivoParticipante>(`${this.baseUrl}/participantes/${idIns}/archivos`);
  }
}
