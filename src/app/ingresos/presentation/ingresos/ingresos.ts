import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { IngresoService } from '../../application/services/ingreso.service';
import { IngresoListResponse, IngresoResumen } from '../../domain/models/ingreso.model';

type ListState    = { type: 'loading' } | { type: 'success'; response: IngresoListResponse } | { type: 'error' } | { type: 'forbidden' };
type ResumenState = { type: 'loading' } | { type: 'success'; data: IngresoResumen } | { type: 'error' };

const LL: ListState    = { type: 'loading' };
const EL: ListState    = { type: 'error' };
const FL: ListState    = { type: 'forbidden' };
const LR: ResumenState = { type: 'loading' };
const ER: ResumenState = { type: 'error' };

@Component({
  selector: 'app-ingresos',
  standalone: true,
  imports: [NgIcon, Pagination, PageTitle, DecimalPipe, FormsModule],
  templateUrl: './ingresos.html',
})
export class Ingresos {
  private service = inject(IngresoService);

  pageIndex = signal(1);
  pageSize  = signal(20);
  desde     = signal('');
  hasta     = signal('');

  desdeInput = '';
  hastaInput = '';

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    desde:     this.desde() || undefined,
    hasta:     this.hasta() || undefined,
  }));

  private listState = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.service.getAll(p).pipe(
        map(r => ({ type: 'success', response: r } as ListState)),
        startWith(LL),
        catchError((err: HttpErrorResponse) => of(err.status === 403 ? FL : EL))
      )),
      startWith(LL),
    ),
    { requireSync: true }
  );

  private resumenState = toSignal(
    this.service.getResumen().pipe(
      map(r => ({ type: 'success', data: r } as ResumenState)),
      startWith(LR),
      catchError(() => of(ER))
    ),
    { requireSync: true }
  );

  get items()        { const s = this.listState(); return s.type === 'success' ? s.response.data : []; }
  get total()        { const s = this.listState(); return s.type === 'success' ? s.response.total : 0; }
  get totalPeriodo() { const s = this.listState(); return s.type === 'success' ? s.response.total_periodo : null; }
  get isLoading()    { return this.listState().type === 'loading'; }
  get hasError()     { return this.listState().type === 'error'; }
  get error()        { return this.listState().type === 'error'; }
  get forbidden()    { return this.listState().type === 'forbidden'; }

  get resumen()        { const s = this.resumenState(); return s.type === 'success' ? s.data : null; }
  get resumenLoading() { return this.resumenState().type === 'loading'; }
  get meses()          { return this.resumen?.meses ?? []; }
  get filtersActive()  { return !!(this.desde() || this.hasta()); }

  onFiltrar(): void {
    this.desde.set(this.desdeInput);
    this.hasta.set(this.hastaInput);
    this.pageIndex.set(1);
  }

  onLimpiar(): void {
    this.desdeInput = '';
    this.hastaInput = '';
    this.desde.set('');
    this.hasta.set('');
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }
}
