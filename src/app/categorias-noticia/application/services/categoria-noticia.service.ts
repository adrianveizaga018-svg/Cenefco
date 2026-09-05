import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CategoriaNoticia, CategoriaNoticiaListResponse, CategoriaNoticiaListParams } from '../../domain/models/categoria-noticia.model';

@Injectable({ providedIn: 'root' })
export class CategoriaNoticiaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/categorias-noticia';

  getAll(params: CategoriaNoticiaListParams = {}): Observable<CategoriaNoticiaListResponse> {
    return this.http.get<CategoriaNoticiaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<CategoriaNoticia> {
    return this.http.get<CategoriaNoticia>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<CategoriaNoticia>): Observable<CategoriaNoticia> {
    return this.http.post<CategoriaNoticia>(this.baseUrl, data);
  }
  update(id: number, data: Partial<CategoriaNoticia>): Observable<CategoriaNoticia> {
    return this.http.put<CategoriaNoticia>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
