import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { SlicePipe } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { InscripcionDiplomadoService } from '../../application/services/inscripcion-diplomado.service';
import { InscripcionDiplomadoListResponse } from '../../domain/models/inscripcion-diplomado.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

const ESTADOS = [
  { value: 'pendiente',   label: 'Pendiente' },
  { value: 'revisado',    label: 'Revisado' },
  { value: 'aceptado',    label: 'Aceptado' },
  { value: 'rechazado',   label: 'Rechazado' },
  { value: 'contactado',  label: 'Contactado' },
  { value: 'inscrito',    label: 'Inscrito' },
];

type S = { type: 'loading' } | { type: 'success'; response: InscripcionDiplomadoListResponse } | { type: 'error'; status?: number; msg?: string } | { type: 'forbidden' };
const L: S = { type: 'loading' };

@Component({
  selector: 'app-inscripciones-diplomado',
  imports: [NgIcon, Pagination, PageTitle, SlicePipe],
  templateUrl: './inscripciones-diplomado.html',
})
export class InscripcionesDiplomado {
  private service = inject(InscripcionDiplomadoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  readonly estados = ESTADOS;

  searchQuery     = signal('');
  estadoFiltro    = signal('');
  pageIndex       = signal(1);
  pageSize        = signal(15);
  private refresh = signal(0);

  private params = computed(() => ({
    query:     this.searchQuery(),
    estado:    this.estadoFiltro() || undefined,
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.service.getAll(p).pipe(
        map(r => ({ type: 'success', response: r } as S)),
        startWith(L),
        catchError((err: HttpErrorResponse) => {
          if (err?.status === 403) return of({ type: 'forbidden' } as S);
          const status = err?.status;
          const msg = err?.error?.message ?? err?.message ?? 'Error desconocido';
          console.error('[InscripcionesDiplomado] HTTP', status, msg, err);
          return of({ type: 'error', status, msg } as S);
        }),
      )),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()       { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()       { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()   { return this.state().type === 'loading'; }
  get hasError()    { return this.state().type === 'error'; }
  get errorStatus() { const s = this.state(); return s.type === 'error' ? s.status : null; }
  get errorMsg()    { const s = this.state(); return s.type === 'error' ? s.msg : null; }
  get forbidden()   { return this.state().type === 'forbidden'; }

  onSearch(e: Event): void {
    this.searchQuery.set((e.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onEstadoChange(e: Event): void {
    this.estadoFiltro.set((e.target as HTMLSelectElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }

  goToDetail(id: number): void {
    this.router.navigate(['/cenefco/inscripcion-diplomado-detail', id]);
  }

  estadoBadgeClass(estado: string): string {
    switch (estado) {
      case 'inscrito':   return 'bg-success/10 text-success';
      case 'aceptado':   return 'bg-success/10 text-success';
      case 'rechazado':  return 'bg-danger/10 text-danger';
      case 'contactado': return 'bg-blue-100 text-blue-700';
      case 'revisado':   return 'bg-warning/10 text-warning';
      default:           return 'bg-default-100 text-default-500';
    }
  }

  estadoLabel(estado: string): string {
    return this.estados.find(e => e.value === estado)?.label ?? estado;
  }

  delete(id: number, nombre: string): void {
    Swal.fire({
      title: '¿Eliminar inscripción a diplomado?',
      html: `Se eliminará el registro de <b>${nombre}</b>. Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => { this.toast.success('Eliminada', 'Inscripción a diplomado eliminada'); this.refresh.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar el registro'),
        });
      }
    });
  }
}
