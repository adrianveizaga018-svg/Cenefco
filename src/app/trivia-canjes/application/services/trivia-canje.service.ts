import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TriviaCanje, TriviaCanjeListResponse, TriviaCanjeListParams } from '../../domain/models/trivia-canje.model';

@Injectable({ providedIn: 'root' })
export class TriviaCanjeService {
  private http = inject(HttpClient);
  private readonly baseUrl = '/api/v1/trivia-canjes';

  getAll(params: TriviaCanjeListParams = {}): Observable<TriviaCanjeListResponse> {
    return this.http.get<TriviaCanjeListResponse>(this.baseUrl, { params: params as Record<string, string | number | boolean> });
  }
  entregar(id: number, nota: string | null): Observable<TriviaCanje> {
    return this.http.patch<TriviaCanje>(`${this.baseUrl}/${id}/entregar`, { nota });
  }
  cancelar(id: number, nota: string | null): Observable<TriviaCanje> {
    return this.http.patch<TriviaCanje>(`${this.baseUrl}/${id}/cancelar`, { nota });
  }
}
