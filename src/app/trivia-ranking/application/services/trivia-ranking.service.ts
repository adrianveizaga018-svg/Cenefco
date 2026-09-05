import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TriviaRankingResponse } from '../../domain/models/trivia-ranking.model';

@Injectable({ providedIn: 'root' })
export class TriviaRankingService {
  private http = inject(HttpClient);

  getRanking(limite: number): Observable<TriviaRankingResponse> {
    return this.http.get<TriviaRankingResponse>('/api/v1/trivia-ranking', {
      params: { limite },
    });
  }
}
