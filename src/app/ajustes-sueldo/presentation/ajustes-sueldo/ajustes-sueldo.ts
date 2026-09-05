import { Component, computed, inject, signal, OnInit } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { DecimalPipe, NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { ToastService } from '../../../common/application/services/toast.service';
import { AjusteSueldoService } from '../../application/services/ajuste-sueldo.service';
import { AjusteSueldo, AjusteSueldoListResponse, TipoAjusteSueldo } from '../../domain/models/ajuste-sueldo.model';
import { EmpleadoService } from '../../../empleados/application/services/empleado.service';
import { Empleado } from '../../../empleados/domain/models/empleado.model';
import { extractErrorMessage } from '../../../utils/http-error';

type S = { type: 'loading' } | { type: 'success'; response: AjusteSueldoListResponse } | { type: 'error' };

const MESES = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];

@Component({
  selector: 'app-ajustes-sueldo',
  imports: [NgIcon, PageTitle, Pagination, NgClass, FormsModule, DecimalPipe],
  templateUrl: './ajustes-sueldo.html',
})
export class AjustesSueldo implements OnInit {
  private service         = inject(AjusteSueldoService);
  private empleadoService = inject(EmpleadoService);
  private toast           = inject(ToastService);

  empleados = signal<Empleado[]>([]);

  pageIndex     = signal(1);
  pageSize      = signal(20);
  filtroEmpleado = signal<number | null>(null);
  filtroAnio    = signal<number | null>(null);
  filtroMes     = signal<number | null>(null);
  filtroAplicado = signal<'todos' | 'pendientes' | 'aplicados'>('todos');
  private refresh = signal(0);

  showForm    = signal(false);
  guardando   = signal(false);
  formEmpleadoId = signal<number | null>(null);
  formAnio    = signal(new Date().getFullYear());
  formMes     = signal(new Date().getMonth() + 1);
  formTipo    = signal<TipoAjusteSueldo>('descuento');
  formMonto   = signal<number | null>(null);
  formMotivo  = signal('');

  readonly meses = MESES;
  readonly anios = Array.from({ length: 6 }, (_, i) => new Date().getFullYear() - 2 + i);

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    empleado_id: this.filtroEmpleado() ?? undefined,
    anio: this.filtroAnio() ?? undefined,
    mes: this.filtroMes() ?? undefined,
    aplicado: this.filtroAplicado() === 'todos' ? undefined : this.filtroAplicado() === 'aplicados',
    refresh: this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(200),
      switchMap(p => this.service.getAll(p).pipe(
        map(r => ({ type: 'success', response: r } as S)),
        startWith({ type: 'loading' } as S),
        catchError(() => of({ type: 'error' } as S)),
      )),
      startWith({ type: 'loading' } as S),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get isError()   { return this.state().type === 'error'; }

  ngOnInit(): void {
    this.empleadoService.getActivos().subscribe({ next: e => this.empleados.set(e) });
  }

  setFiltroAplicado(f: 'todos' | 'pendientes' | 'aplicados'): void {
    this.filtroAplicado.set(f);
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }

  nombreMes(mes: number): string { return this.meses[mes - 1] ?? String(mes); }

  abrirForm(): void {
    this.formEmpleadoId.set(this.empleados()[0]?.id ?? null);
    this.formAnio.set(new Date().getFullYear());
    this.formMes.set(new Date().getMonth() + 1);
    this.formTipo.set('descuento');
    this.formMonto.set(null);
    this.formMotivo.set('');
    this.showForm.set(true);
  }

  cerrarForm(): void { this.showForm.set(false); }

  guardar(): void {
    const empleadoId = this.formEmpleadoId();
    const monto = this.formMonto();
    const motivo = this.formMotivo().trim();

    if (!empleadoId) { this.toast.error('Error', 'Selecciona un empleado'); return; }
    if (!monto || monto <= 0) { this.toast.error('Error', 'Ingresa un monto válido'); return; }
    if (!motivo) { this.toast.error('Error', 'Indica el motivo del ajuste'); return; }

    this.guardando.set(true);
    this.service.create({
      empleado_id: empleadoId,
      anio:        this.formAnio(),
      mes:         this.formMes(),
      tipo:        this.formTipo(),
      monto,
      motivo,
    }).subscribe({
      next: () => {
        this.toast.success('Ajuste registrado', 'Se aplicará en la próxima planilla de ese mes.');
        this.guardando.set(false);
        this.showForm.set(false);
        this.pageIndex.set(1);
        this.refresh.update(n => n + 1);
      },
      error: (err) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar el ajuste'));
        this.guardando.set(false);
      },
    });
  }

  eliminar(a: AjusteSueldo): void {
    if (a.aplicado) return;

    Swal.fire({
      title: '¿Eliminar este ajuste?',
      text: `${a.empleado_nombre} — Bs. ${a.monto} (${a.motivo})`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(a.id).subscribe({
          next: () => { this.toast.success('Eliminado', 'El ajuste fue eliminado'); this.refresh.update(n => n + 1); },
          error: (err) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar')),
        });
      }
    });
  }
}
