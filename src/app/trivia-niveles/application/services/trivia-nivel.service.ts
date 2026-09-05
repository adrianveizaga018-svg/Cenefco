import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TriviaNivel } from '../../domain/models/trivia-nivel.model';

@Injectable({ providedIn: 'root' })
export class TriviaNivelService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/trivia-niveles';

  getByCategoria(categoriaId: number): Observable<TriviaNivel[]> {
    return this.http.get<TriviaNivel[]>(this.baseUrl, { params: { categoria_id: categoriaId } });
  }
  getById(id: number): Observable<TriviaNivel> {
    return this.http.get<TriviaNivel>(`${this.baseUrl}/${id}`);
  }
  create(data: Partial<TriviaNivel>): Observable<TriviaNivel> {
    return this.http.post<TriviaNivel>(this.baseUrl, data);
  }
  update(id: number, data: Partial<TriviaNivel>): Observable<TriviaNivel> {
    return this.http.put<TriviaNivel>(`${this.baseUrl}/${id}`, data);
  }
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }
}
