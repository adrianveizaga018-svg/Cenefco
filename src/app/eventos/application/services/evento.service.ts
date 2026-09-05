import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import {
  Evento,
  EventoListParams,
  EventoListResponse,
  CreateEventoPayload,
} from '../../domain/models/evento.model';

@Injectable({ providedIn: 'root' })
export class EventoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/eventos';

  readonly tiposEvento = [
    'Conferencia', 'Taller', 'Seminario', 'Feria',
    'Graduación', 'Congreso', 'Webinar', 'Inauguración',
  ];

  getAll(params: EventoListParams = {}): Observable<EventoListResponse> {
    let httpParams = new HttpParams();
    if (params.pageIndex != null) httpParams = httpParams.set('pageIndex', params.pageIndex);
    if (params.pageSize  != null) httpParams = httpParams.set('pageSize',  params.pageSize);
    if (params.query)             httpParams = httpParams.set('query',     params.query);
    return this.http.get<EventoListResponse>(this.baseUrl, { params: httpParams });
  }

  getById(id: number): Observable<Evento> {
    return this.http.get<Evento>(`${this.baseUrl}/${id}`);
  }
  getBySlug(slug: string): Observable<Evento> {
    return this.http.get<Evento>(`${this.baseUrl}/slug/${slug}`);
  }

  create(data: CreateEventoPayload): Observable<Evento> {
    return this.http.post<Evento>(this.baseUrl, data);
  }

  update(id: number, data: Partial<CreateEventoPayload>): Observable<Evento> {
    return this.http.put<Evento>(`${this.baseUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  uploadImage(file: File): Observable<{ url: string }> {
    const fd = new FormData();
    fd.append('file', file);
    return this.http.post<{ url: string }>('/api/v1/upload/image', fd);
  }
}
