import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, distinctUntilChanged, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { NotificacionSistemaService } from '../../application/services/notificacion-sistema.service';
import { NotificacionSistemaListResponse } from '../../domain/models/notificacion-sistema.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type S = { type: 'loading' } | { type: 'success'; response: NotificacionSistemaListResponse } | { type: 'error' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' };

const TIPO_ICONOS: Record<string, string> = {
  pago_registrado:        'lucideBanknote',
  pago_vencido:           'lucideCircleAlert',
  pago_observado:         'lucideAlertTriangle',
  cuota_proxima:          'lucideAlarmClock',
  inscripcion_registrada: 'lucideClipboardList',
  nota_publicada:         'lucideGraduationCap',
  certificado_listo:      'lucideAward',
  comunicado:             'lucideMegaphone',
  actividad:              'lucideActivity',
  stock_bajo:             'lucidePackage',
  aprobacion_pendiente:   'lucideCircleAlert',
  bienvenida:             'lucideSparkles',
};

@Component({
  selector: 'app-notificaciones-sistema',
  imports: [Pagination, PageTitle, NgIcon, NgClass, FormsModule, RouterLink],
  templateUrl: './notificaciones-sistema.html',
})
export class NotificacionesSistema {
  private service = inject(NotificacionSistemaService);
  private toast   = inject(ToastService);

  pageIndex       = signal(1);
  pageSize        = signal(30);
  search          = signal('');
  filtro          = signal<'todas' | 'no_leidas' | 'leidas'>('todas');
  private refresh = signal(0);

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.search(),
    leida:     this.filtro() === 'todas' ? undefined : (this.filtro() === 'leidas' ? 1 : 0),
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
      distinctUntilChanged((a, b) => JSON.stringify(a) === JSON.stringify(b)),
      switchMap(p => this.service.getAll(p).pipe(map(r => ({ type: 'success', response: r } as S)), startWith(L), catchError(() => of(E)))),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get isError()   { return this.state().type === 'error'; }

  get totalNoLeidas() { return this.items.filter(i => !i.leida).length; }
  get totalLeidas()   { return this.items.filter(i => i.leida).length; }

  onPageChange(p: number): void { this.pageIndex.set(p); }
  onSearch(value: string): void { this.search.set(value); this.pageIndex.set(1); }

  setFiltro(f: 'todas' | 'no_leidas' | 'leidas'): void {
    this.filtro.set(f);
    this.pageIndex.set(1);
  }

  iconoTipo(tipo: string | null): string {
    return tipo ? (TIPO_ICONOS[tipo] ?? 'lucideBell') : 'lucideBell';
  }

  tipoLabel(tipo: string | null): string {
    if (!tipo) return '—';
    return tipo.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase());
  }

  formatFecha(f: string | null): string {
    if (!f) return '—';
    try {
      return new Date(f).toLocaleString('es-BO', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
      });
    } catch { return f; }
  }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar notificación?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => { this.toast.success('Eliminado', 'Notificación eliminada'); this.refresh.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar'),
        });
      }
    });
  }
}
