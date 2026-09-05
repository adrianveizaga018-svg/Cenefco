import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { NotificacionSistema, NotificacionSistemaListResponse, NotificacionSistemaListParams } from '../../domain/models/notificacion-sistema.model';

@Injectable({ providedIn: 'root' })
export class NotificacionSistemaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/notificaciones';

  getAll(p: NotificacionSistemaListParams): Observable<NotificacionSistemaListResponse> {
    let params = new HttpParams();
    if (p.pageIndex)          params = params.set('pageIndex', p.pageIndex);
    if (p.pageSize)           params = params.set('pageSize', p.pageSize);
    if (p.query)              params = params.set('query', p.query);
    if (p.id_usuario)         params = params.set('id_usuario', p.id_usuario);
    if (p.leida !== undefined) params = params.set('leida', p.leida);
    return this.http.get<NotificacionSistemaListResponse>(this.baseUrl, { params });
  }

  getById(id: number): Observable<NotificacionSistema> {
    return this.http.get<NotificacionSistema>(`${this.baseUrl}/${id}`);
  }

  create(data: Partial<NotificacionSistema>): Observable<NotificacionSistema> {
    return this.http.post<NotificacionSistema>(this.baseUrl, data);
  }

  update(id: number, data: Partial<NotificacionSistema>): Observable<NotificacionSistema> {
    return this.http.put<NotificacionSistema>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
