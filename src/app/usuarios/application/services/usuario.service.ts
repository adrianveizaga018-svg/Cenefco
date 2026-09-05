import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Usuario, UsuarioListParams, UsuarioListResponse,
  CreateUsuarioPayload, UpdateUsuarioPayload, Rol
} from '../../domain/models/usuario.model';

@Injectable({ providedIn: 'root' })
export class UsuarioService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/usuarios';

  getAll(params: UsuarioListParams = {}): Observable<UsuarioListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    if (params.origen)            httpParams = httpParams.set('origen',    params.origen);
    return this.http.get<UsuarioListResponse>(this.baseUrl, { params: httpParams });
  }

  getById(id: number): Observable<Usuario> {
    return this.http.get<Usuario>(`${this.baseUrl}/${id}`);
  }

  getRoles(): Observable<Rol[]> {
    return this.http.get<Rol[]>('/api/v1/roles');
  }

  create(data: CreateUsuarioPayload): Observable<Usuario> {
    return this.http.post<Usuario>(this.baseUrl, data);
  }

  update(id: number, data: UpdateUsuarioPayload): Observable<Usuario> {
    return this.http.put<Usuario>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
