import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { SlicePipe, DecimalPipe, TitleCasePipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { HttpErrorResponse } from '@angular/common/http';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { InscripcionService } from '../../application/services/inscripcion.service';
import { Inscripcion, InscripcionListResponse } from '../../domain/models/inscripcion.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { SettingsService } from '../../../common/application/services/settings.service';
import { CobroEstadoSettings } from '../../../common/domain/models/settings.model';
import Swal from 'sweetalert2';

interface ModeloOption { id_cartamod: number; nombremodelo: string; }

type S = { type: 'loading' } | { type: 'success'; response: InscripcionListResponse } | { type: 'error'; status?: number; msg?: string } | { type: 'forbidden' };
const L: S = { type: 'loading' };

@Component({
  selector: 'app-inscripciones',
  imports: [NgIcon, Pagination, PageTitle, RouterLink, SlicePipe, DecimalPipe, TitleCasePipe],
  templateUrl: './inscripciones.html',
})
export class Inscripciones {
  private service         = inject(InscripcionService);
  private toast           = inject(ToastService);
  private router          = inject(Router);
  private route           = inject(ActivatedRoute);
  private http            = inject(HttpClient);
  private settingsService = inject(SettingsService);

  searchQuery     = signal('');
  pageIndex       = signal(1);
  pageSize        = signal(15);
  programaId      = signal<number | null>(null);
  private refresh = signal(0);

  modelos  = signal<ModeloOption[]>([]);
  modeloId = signal<number | null>(null);

  cobroEstadoSettings = signal<CobroEstadoSettings | null>(null);

  constructor() {
    const pid = this.route.snapshot.queryParamMap.get('programa_id');
    if (pid) this.programaId.set(Number(pid));

    this.http.get<{ data: ModeloOption[] }>('/api/v1/cartas-modelo', {
      params: { pageSize: '100', conInactivos: 'false' }
    }).subscribe({ next: r => this.modelos.set(r.data) });

    this.settingsService.getCobroEstadoSettings().subscribe({
      next: settings => this.cobroEstadoSettings.set(settings),
      error: () => this.cobroEstadoSettings.set(null),
    });
  }

  private params = computed(() => ({
    query:        this.searchQuery(),
    pageIndex:    this.pageIndex(),
    pageSize:     this.pageSize(),
    refresh:      this.refresh(),
    conInactivos: true,
    programa_id: this.programaId() ?? undefined,
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
          console.error('[Inscripciones] HTTP', status, msg, err);
          return of({ type: 'error', status, msg } as S);
        }),
      )),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()      { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()      { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()  { return this.state().type === 'loading'; }
  get hasError()   { return this.state().type === 'error'; }
  get errorStatus(){ const s = this.state(); return s.type === 'error' ? s.status : null; }
  get errorMsg()   { const s = this.state(); return s.type === 'error' ? s.msg : null; }
  get forbidden()  { return this.state().type === 'forbidden'; }

  onSearch(e: Event): void {
    this.searchQuery.set((e.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }

  goToDetail(id: number): void {
    this.router.navigate(['/cenefco/inscripcion-detail', id]);
  }

  pagosBadgeClass(pagadas: number, totales: number): string {
    if (!totales) return 'bg-default-100 text-default-500';
    if (pagadas === 0)              return 'bg-danger/10 text-danger';
    if (pagadas >= totales)         return 'bg-success/10 text-success';
    return 'bg-warning/10 text-warning';
  }

  private readonly cobroEstadoFallback: CobroEstadoSettings = {
    completo_label: 'Pagado', completo_color: 'success',
    parcial_label: 'Parcial', parcial_color: 'warning',
    sin_pagos_label: 'Sin pagos', sin_pagos_color: 'danger',
  };

  estadoCobro(item: Inscripcion): { label: string; color: string } | null {
    const costoCurso = item.curso_costo_monto;
    if (!costoCurso) return null;

    const s = this.cobroEstadoSettings() ?? this.cobroEstadoFallback;



    const pagadoCentavos    = Math.round(item.total_pagado * 100);
    const esperadoCentavos  = Math.round(costoCurso * 100);

    if (pagadoCentavos <= 0) return { label: s.sin_pagos_label, color: s.sin_pagos_color };
    if (pagadoCentavos >= esperadoCentavos) return { label: s.completo_label, color: s.completo_color };
    return { label: s.parcial_label, color: s.parcial_color };
  }

  delete(id: number, nombre: string): void {
    Swal.fire({
      title: '¿Eliminar inscripción?',
      html: `Se eliminará permanentemente la inscripción de <b>${nombre}</b>. Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => { this.toast.success('Eliminada', 'Inscripción eliminada correctamente'); this.refresh.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar la inscripción'),
        });
      }
    });
  }
}
