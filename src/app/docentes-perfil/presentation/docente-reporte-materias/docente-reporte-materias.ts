import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DocentePerfilService } from '../../application/services/docente-perfil.service';
import { DocenteReporteMateriasResponse } from '../../domain/models/docente-perfil.model';

type ApiState = { type: 'loading' } | { type: 'success'; response: DocenteReporteMateriasResponse } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' }; const ERROR: ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-docente-reporte-materias',
  imports: [NgIcon, PageTitle, RouterLink],
  templateUrl: './docente-reporte-materias.html',
})
export class DocenteReporteMaterias {
  private service = inject(DocentePerfilService);

  gestionInput = signal('');
  periodoInput = signal('');

  private filtrosAplicados = signal({ gestion: '', periodo: '', ts: 0 });

  private state = toSignal(
    toObservable(this.filtrosAplicados).pipe(
      switchMap(f => this.service.getReporteMaterias({ gestion: f.gestion, periodo: f.periodo }).pipe(
        map(r => ({ type: 'success', response: r } as ApiState)),
        startWith(LOADING),
        catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
      )),
      startWith(LOADING),
    ),
    { requireSync: true },
  );

  get docentes()   { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get isLoading()  { return this.state().type === 'loading'; }
  get error()      { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }
  get totalDocentes() { return this.docentes.length; }
  get totalMaterias() { return this.docentes.reduce((acc, d) => acc + d.total_materias, 0); }
  get docentesConMaterias() { return this.docentes.filter(d => d.total_materias > 0).length; }

  aplicarFiltros(): void {
    this.filtrosAplicados.set({
      gestion: this.gestionInput(),
      periodo: this.periodoInput(),
      ts: Date.now(),
    });
  }

  limpiarFiltros(): void {
    this.gestionInput.set('');
    this.periodoInput.set('');
    this.filtrosAplicados.set({ gestion: '', periodo: '', ts: Date.now() });
  }

  onGestionInput(e: Event): void  { this.gestionInput.set((e.target as HTMLInputElement).value); }
  onPeriodoInput(e: Event): void  { this.periodoInput.set((e.target as HTMLInputElement).value); }
  onKeyEnter(e: KeyboardEvent): void { if (e.key === 'Enter') this.aplicarFiltros(); }
}
