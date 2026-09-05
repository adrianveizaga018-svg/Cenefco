import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { EventoFoto, EventoFotoListResponse, EventoFotoListParams } from '../../domain/models/evento-foto.model';

@Injectable({ providedIn: 'root' })
export class EventoFotoService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/eventos-fotos';

  getAll(params: EventoFotoListParams = {}): Observable<EventoFotoListResponse> {
    return this.http.get<EventoFotoListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<EventoFoto> {
    return this.http.get<EventoFoto>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<EventoFoto>): Observable<EventoFoto> {
    return this.http.post<EventoFoto>(this.baseUrl, data);
  }
  update(id: number, data: Partial<EventoFoto>): Observable<EventoFoto> {
    return this.http.put<EventoFoto>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
