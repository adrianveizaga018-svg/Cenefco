import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TriviaCategoria, TriviaCategoriaListResponse, TriviaCategoriaListParams } from '../../domain/models/trivia-categoria.model';

@Injectable({ providedIn: 'root' })
export class TriviaCategoriaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/trivia-categorias';

  getAll(params: TriviaCategoriaListParams = {}): Observable<TriviaCategoriaListResponse> {
    return this.http.get<TriviaCategoriaListResponse>(this.baseUrl, { params: params as Record<string, string | number | boolean> });
  }
  getById(id: number): Observable<TriviaCategoria> {
    return this.http.get<TriviaCategoria>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TriviaCategoria>): Observable<TriviaCategoria> {
    return this.http.post<TriviaCategoria>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TriviaCategoria>): Observable<TriviaCategoria> {
    return this.http.put<TriviaCategoria>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
