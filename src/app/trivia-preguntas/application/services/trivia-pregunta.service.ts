import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TriviaPregunta, TriviaPreguntaListResponse, TriviaPreguntaListParams } from '../../domain/models/trivia-pregunta.model';

@Injectable({ providedIn: 'root' })
export class TriviaPreguntaService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/trivia-preguntas';

  getAll(params: TriviaPreguntaListParams = {}): Observable<TriviaPreguntaListResponse> {
    return this.http.get<TriviaPreguntaListResponse>(this.baseUrl, { params: params as Record<string, string | number> });
  }
  getById(id: number): Observable<TriviaPregunta> {
    return this.http.get<TriviaPregunta>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TriviaPregunta>): Observable<TriviaPregunta> {
    return this.http.post<TriviaPregunta>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TriviaPregunta>): Observable<TriviaPregunta> {
    return this.http.put<TriviaPregunta>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
