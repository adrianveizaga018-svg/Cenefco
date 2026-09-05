import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Formulario,
  FormularioListParams,
  FormularioListResponse,
  CreateFormularioPayload,
} from '../../domain/models/formulario.model';

@Injectable({ providedIn: 'root' })
export class FormularioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/formularios';

  getAll(params: FormularioListParams = {}): Observable<FormularioListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return this.http.get<FormularioListResponse>(this.baseUrl, { params: httpParams });
  }

  getActivos(): Observable<Formulario[]> {
    return this.http.get<Formulario[]>(`${this.baseUrl}/activos`);
  }

  getById(id: number): Observable<Formulario> {
    return this.http.get<Formulario>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateFormularioPayload): Observable<Formulario> {
    return this.http.post<Formulario>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateFormularioPayload>): Observable<Formulario> {
    return this.http.put<Formulario>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
