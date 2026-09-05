import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  UsuarioMoodle, UsuarioMoodleListParams, UsuarioMoodleListResponse,
  CreateUsuarioMoodlePayload, UpdateUsuarioMoodlePayload
} from '../../domain/models/usuario-moodle.model';

@Injectable({ providedIn: 'root' })
export class UsuarioMoodleService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/usuarios-moodle';

  getAll(params: UsuarioMoodleListParams = {}): Observable<UsuarioMoodleListResponse> {
    let p = new HttpParams();
    if (params.pageIndex != null) p = p.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) p = p.set('pageSize',  params.pageSize);
    if (params.id_us     != null) p = p.set('id_us',     params.id_us);
    if (params.id_moodle != null) p = p.set('id_moodle', params.id_moodle);
    return this.http.get<UsuarioMoodleListResponse>(this.baseUrl, { params: p });
  }

  getById(id: number): Observable<UsuarioMoodle> {
    return this.http.get<UsuarioMoodle>(`${this.baseUrl}/${id}`);
  }

  create(data: CreateUsuarioMoodlePayload): Observable<UsuarioMoodle> {
    return this.http.post<UsuarioMoodle>(this.baseUrl, data);
  }

  update(id: number, data: UpdateUsuarioMoodlePayload): Observable<UsuarioMoodle> {
    return this.http.put<UsuarioMoodle>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
