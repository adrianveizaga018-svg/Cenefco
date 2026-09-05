import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { CalendarioAcademicoService } from '../../application/services/calendario-academico.service';
import { CalendarioAcademico as CalendarioItem, CalendarioAcademicoListResponse, TIPOS_EVENTO, TipoEvento } from '../../domain/models/calendario-academico.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type Vista = 'lista' | 'calendario';
type PosEvento = 'unico' | 'inicio' | 'medio' | 'fin';
type S = { type: 'loading' } | { type: 'success'; response: CalendarioAcademicoListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

const DIAS  = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

interface DiaGrid { fecha: Date; otroMes: boolean; esHoy: boolean; }

@Component({
  selector: 'app-calendario-academico',
  imports: [PageTitle, Pagination, RouterLink, NgIcon],
  templateUrl: './calendario-academico.html',
})
export class CalendarioAcademico {
  private service = inject(CalendarioAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  readonly tipos   = TIPOS_EVENTO;
  readonly diasSem = DIAS;
  readonly hoy     = new Date();

  vista = signal<Vista>('lista');

  pageIndex       = signal(1);
  pageSize        = signal(20);
  query           = signal('');
  filtroTipo      = signal('');
  private refresh = signal(0);

  exportingPdf   = signal(false);
  exportingExcel = signal(false);

  exportPdf(): void {
    this.exportingPdf.set(true);
    this.service.exportPdf({ query: this.query(), tipo: this.filtroTipo() }).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = 'calendario-academico-listado.pdf';
        a.click();
        URL.revokeObjectURL(url);
        this.exportingPdf.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el PDF.');
        this.exportingPdf.set(false);
      },
    });
  }

  exportExcel(): void {
    this.exportingExcel.set(true);
    this.service.exportExcel({ query: this.query(), tipo: this.filtroTipo() }).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = 'calendario-academico-listado.xlsx';
        a.click();
        URL.revokeObjectURL(url);
        this.exportingExcel.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el Excel.');
        this.exportingExcel.set(false);
      },
    });
  }

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.query(),
    tipo:      this.filtroTipo() || undefined,
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
      switchMap(p => this.service.getAll(p).pipe(
        map(r => ({ type: 'success', response: r } as S)),
        startWith(L),
        catchError((err: HttpErrorResponse) => of(err.status === 403 ? F : E)),
      )),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get hasError()  { return this.state().type === 'error'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  mesActual       = signal(this.hoy.getMonth());
  anioActual      = signal(this.hoy.getFullYear());
  diaSeleccionado = signal<Date | null>(null);
  filtroTipoCal   = signal('');

  private todosTrigger = signal(0);
  private todosRaw = toSignal(
    toObservable(this.todosTrigger).pipe(
      switchMap(() => this.service.getAll({ pageSize: 500 }).pipe(
        map(r => r.data),
        catchError(() => of([] as CalendarioItem[])),
      )),
      startWith([] as CalendarioItem[]),
    ),
    { requireSync: true }
  );

  private todosState = computed(() => {
    const f = this.filtroTipoCal();
    return f ? this.todosRaw().filter(e => e.tipo === f) : this.todosRaw();
  });

  private eventosPorDiaMap = computed(() => {
    const map = new Map<string, CalendarioItem[]>();
    for (const ev of this.todosState()) {
      const inicio = this.soloFecha(new Date(ev.fecha_inicio));
      const fin    = ev.fecha_fin ? this.soloFecha(new Date(ev.fecha_fin)) : new Date(inicio);
      let curr = new Date(inicio);
      while (curr <= fin) {
        const key = this.toKey(curr);
        if (!map.has(key)) map.set(key, []);
        map.get(key)!.push(ev);
        curr.setDate(curr.getDate() + 1);
      }
    }
    return map;
  });

  estadosMes = computed(() => {
    const y = this.anioActual(), m = this.mesActual();
    const evs = this.todosRaw().filter(e => {
      const d = new Date(e.fecha_inicio);
      return d.getFullYear() === y && d.getMonth() === m;
    });
    const counts = new Map<string, number>();
    for (const ev of evs) {
      const k = ev.tipo ?? 'otro';
      counts.set(k, (counts.get(k) ?? 0) + 1);
    }
    return { total: evs.length, porTipo: counts };
  });

  tituloMes = computed(() => `${MESES[this.mesActual()]} ${this.anioActual()}`);

  diasGrid = computed((): DiaGrid[] => {
    const year = this.anioActual(), month = this.mesActual();
    let primerDia = new Date(year, month, 1).getDay();
    primerDia = primerDia === 0 ? 6 : primerDia - 1;
    const diasEnMes       = new Date(year, month + 1, 0).getDate();
    const diasMesAnterior = new Date(year, month, 0).getDate();
    const dias: DiaGrid[] = [];

    for (let i = primerDia - 1; i >= 0; i--)
      dias.push({ fecha: new Date(year, month - 1, diasMesAnterior - i), otroMes: true, esHoy: false });

    for (let d = 1; d <= diasEnMes; d++) {
      const fecha = new Date(year, month, d);
      dias.push({ fecha, otroMes: false, esHoy: this.mismaFecha(fecha, this.hoy) });
    }
    const total = dias.length <= 35 ? 35 : 42;
    let next = 1;
    while (dias.length < total)
      dias.push({ fecha: new Date(year, month + 1, next++), otroMes: true, esHoy: false });

    return dias;
  });

  eventosDelDia = computed(() => {
    const sel = this.diaSeleccionado();
    if (!sel) return [];
    return this.eventosPorDiaMap().get(this.toKey(sel)) ?? [];
  });

  eventosEnCelda(dia: Date): CalendarioItem[] {
    return this.eventosPorDiaMap().get(this.toKey(dia)) ?? [];
  }

  posEvento(dia: Date, ev: CalendarioItem): PosEvento {
    const inicio = this.soloFecha(new Date(ev.fecha_inicio));
    const fin    = ev.fecha_fin ? this.soloFecha(new Date(ev.fecha_fin)) : new Date(inicio);
    const esInicio = this.mismaFecha(dia, inicio);
    const esFin    = this.mismaFecha(dia, fin);
    if (esInicio && esFin) return 'unico';
    if (esInicio) return 'inicio';
    if (esFin)   return 'fin';
    return 'medio';
  }

  barClasses(pos: PosEvento): string {
    return {
      unico:  'mx-1 rounded-full px-1.5',
      inicio: 'ml-1 mr-0 rounded-l-full pl-1.5 pr-0',
      medio:  'mx-0 rounded-none px-0',
      fin:    'ml-0 mr-1 rounded-r-full pl-0 pr-1.5',
    }[pos];
  }

  esPasado(ev: CalendarioItem): boolean {
    const fin = ev.fecha_fin ? new Date(ev.fecha_fin) : new Date(ev.fecha_inicio);
    return this.soloFecha(fin) < this.soloFecha(this.hoy);
  }

  seleccionarDia(dia: DiaGrid): void {
    if (dia.otroMes) {
      this.mesActual.set(dia.fecha.getMonth());
      this.anioActual.set(dia.fecha.getFullYear());
    }
    const prev = this.diaSeleccionado();
    this.diaSeleccionado.set(prev && this.mismaFecha(prev, dia.fecha) ? null : dia.fecha);
  }

  crearDesdeCalendario(dia: DiaGrid, event: MouseEvent): void {
    event.stopPropagation();
    const fecha = dia.fecha.toISOString().slice(0, 10);
    this.router.navigate(['/cenefco/calendario-create'], { queryParams: { fecha } });
  }

  esDiaSeleccionado(dia: Date): boolean {
    const sel = this.diaSeleccionado();
    return !!sel && this.mismaFecha(sel, dia);
  }

  mesAnterior(): void {
    if (this.mesActual() === 0) { this.mesActual.set(11); this.anioActual.update(a => a - 1); }
    else this.mesActual.update(m => m - 1);
    this.diaSeleccionado.set(null);
  }

  mesSiguiente(): void {
    if (this.mesActual() === 11) { this.mesActual.set(0); this.anioActual.update(a => a + 1); }
    else this.mesActual.update(m => m + 1);
    this.diaSeleccionado.set(null);
  }

  irAHoy(): void {
    this.mesActual.set(this.hoy.getMonth());
    this.anioActual.set(this.hoy.getFullYear());
    this.diaSeleccionado.set(null);
  }

  setVista(v: Vista): void {
    this.vista.set(v);
    if (v === 'calendario') this.todosTrigger.update(n => n + 1);
  }

  onFiltroTipoCal(e: Event): void {
    this.filtroTipoCal.set((e.target as HTMLSelectElement).value);
  }

  onSearch(e: Event): void { this.query.set((e.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onFiltroTipo(e: Event): void { this.filtroTipo.set((e.target as HTMLSelectElement).value); this.pageIndex.set(1); }
  onPageChange(p: number): void { this.pageIndex.set(p); }

  tipoLabel(tipo: TipoEvento | null): string { return TIPOS_EVENTO.find(t => t.value === tipo)?.label ?? tipo ?? '—'; }
  tipoColor(tipo: TipoEvento | null): string { return TIPOS_EVENTO.find(t => t.value === tipo)?.color ?? '#6b7280'; }
  eventoColor(e: CalendarioItem): string     { return e.color || this.tipoColor(e.tipo); }

  formatFecha(fecha: string): string {
    if (!fecha) return '—';
    return new Date(fecha).toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  formatDiaSel(fecha: Date): string {
    return fecha.toLocaleDateString('es-BO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar evento?', text: 'Esta acción no se puede deshacer.',
      icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => {
            this.toast.success('Eliminado', 'Evento eliminado');
            this.refresh.update(n => n + 1);
            this.todosTrigger.update(n => n + 1);
            this.diaSeleccionado.set(null);
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar'),
        });
      }
    });
  }

  private mismaFecha(a: Date, b: Date): boolean {
    return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  }

  private soloFecha(d: Date): Date {
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
  }

  private toKey(d: Date): string {
    return `${d.getFullYear()}-${d.getMonth()}-${d.getDate()}`;
  }
}
