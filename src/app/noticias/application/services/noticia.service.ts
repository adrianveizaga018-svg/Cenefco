import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Noticia, NoticiaListResponse, NoticiaListParams } from '../../domain/models/noticia.model';

@Injectable({ providedIn: 'root' })
export class NoticiaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/noticias';

  getAll(params: NoticiaListParams = {}): Observable<NoticiaListResponse> {
    return this.http.get<NoticiaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<Noticia> {
    return this.http.get<Noticia>(`${this.baseUrl}/${id}`);
  }
  getBySlug(slug: string): Observable<Noticia> {
    return this.http.get<Noticia>(`${this.baseUrl}/slug/${slug}`);
  }
  create(data: Partial<Noticia>): Observable<Noticia> {
    return this.http.post<Noticia>(this.baseUrl, data);
  }
  update(id: number, data: Partial<Noticia>): Observable<Noticia> {
    return this.http.put<Noticia>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
