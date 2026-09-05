import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DirectorioArchivoService } from '../../application/services/directorio-archivo.service';
import { ParticipanteDirectorioListResponse } from '../../domain/models/directorio-archivo.model';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: ParticipanteDirectorioListResponse }
  | { type: 'error' } | { type: 'forbidden' };

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' };
const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-directorio-participantes',
  imports: [NgIcon, Pagination, PageTitle, RouterLink],
  templateUrl: './directorio-participantes.html',
  styles: ``
})
export class DirectorioParticipantes {
  private route = inject(ActivatedRoute);
  private directorioService = inject(DirectorioArchivoService);

  idImp = Number(this.route.snapshot.paramMap.get('idImp'));

  searchQuery = signal('');
  pageIndex   = signal(1);
  pageSize    = signal(10);

  private params = computed(() => ({
    query:     this.searchQuery(),
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.directorioService.getParticipantes(this.idImp, p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith(LOADING),
          catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
        )
      ),
      startWith(LOADING),
    ),
    { requireSync: true }
  );

  get participantes() { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()          { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()       { return this.state().type === 'loading'; }
  get error()           { return this.state().type === 'error'; }
  get forbidden()       { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void {
    this.searchQuery.set((event.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void {
    this.pageIndex.set(page);
  }
}
