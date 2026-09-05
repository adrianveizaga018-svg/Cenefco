import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  GeneradorFormulario,
  GeneradorFormularioListParams,
  GeneradorFormularioListResponse,
  SaveFormularioPayload,
} from '../../domain/models/generador-formulario.model';

@Injectable({ providedIn: 'root' })
export class GeneradorFormularioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/formularios';

  getAll(params: GeneradorFormularioListParams = {}): Observable<GeneradorFormularioListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.query)             p = p.set('query',     params.query);
    return this.http.get<GeneradorFormularioListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<GeneradorFormulario> {
    return this.http.get<GeneradorFormulario>(`${this.baseUrl}/${id}`);
  }

  create(data: SaveFormularioPayload): Observable<GeneradorFormulario> {
    return this.http.post<GeneradorFormulario>(this.baseUrl, data);
  }

  update(id: number, data: Partial<SaveFormularioPayload>): Observable<GeneradorFormulario> {
    return this.http.put<GeneradorFormulario>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
