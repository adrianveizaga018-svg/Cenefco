import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaRankingService } from '../../application/services/trivia-ranking.service';
import { TriviaRankingItem } from '../../domain/models/trivia-ranking.model';

type ApiState = { type: 'loading' } | { type: 'success'; data: TriviaRankingItem[] } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-trivia-ranking',
  imports: [NgIcon, PageTitle, FormsModule],
  templateUrl: './trivia-ranking.html',
  styles: ``
})
export class TriviaRanking {
  private service = inject(TriviaRankingService);

  limite = signal(20);
  private refreshTrigger = signal(0);

  private state = toSignal(
    toObservable(computed(() => ({ limite: this.limite(), refresh: this.refreshTrigger() }))).pipe(
      switchMap(p => this.service.getRanking(p.limite).pipe(
        map(response => ({ type: 'success', data: response.data } as ApiState)),
        startWith(LOADING), catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
      )), startWith(LOADING),
    ), { requireSync: true }
  );

  get ranking()  { const s = this.state(); return s.type === 'success' ? s.data : []; }
  get isLoading(){ return this.state().type === 'loading'; }
  get error()    { return this.state().type === 'error'; }
  get forbidden(){ return this.state().type === 'forbidden'; }

  onLimiteChange(valor: number | string): void {
    this.limite.set(Number(valor));
  }

  refrescar(): void {
    this.refreshTrigger.update(n => n + 1);
  }

  medalla(posicion: number): string | null {
    return ({ 1: '🥇', 2: '🥈', 3: '🥉' } as Record<number, string>)[posicion] ?? null;
  }
}
