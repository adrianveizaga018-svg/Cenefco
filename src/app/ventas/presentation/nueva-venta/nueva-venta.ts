import { Component, inject, signal, computed, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { InscripcionService } from '../../../inscripciones/application/services/inscripcion.service';
import { VentaService } from '../../application/services/venta.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import type { MetodoPago } from '../../domain/models/venta.model';

interface EstudianteOption { id_us: number; nombre: string; appaterno: string | null; ci: string | null; email: string | null; celular: string | null; }
interface ImparticionOption { id_imp: number; periodo: string | null; gestion: string | null; materia_nombre: string | null; id_mat: number | null; docente_nombre: string | null; id_programa: number | null; }
interface PlanOption { id_plan: number; titulo: string; nro_cuotas: number; costo: string | null; costo_por_cuota: string | null; }
interface UsuarioOption { id: number; nombre: string; apellido: string; }

const METODOS_PAGO: { value: MetodoPago; label: string }[] = [
  { value: 'efectivo',          label: 'Efectivo' },
  { value: 'deposito_bancario', label: 'Depósito Bancario' },
  { value: 'qr',                label: 'QR' },
];

@Component({
  selector: 'app-nueva-venta',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './nueva-venta.html',
})
export class NuevaVenta implements OnInit {
  private inscripcionService = inject(InscripcionService);
  private ventaService       = inject(VentaService);
  private toast              = inject(ToastService);
  private router             = inject(Router);
  private fb                 = inject(FormBuilder);
  private http               = inject(HttpClient);

  paso = signal<1 | 2 | 3>(1);

  imparticiones = signal<ImparticionOption[]>([]);
  planes        = signal<PlanOption[]>([]);
  usuarios      = signal<UsuarioOption[]>([]);
  loadingPlanes = signal(false);
  buscandoEst   = signal(false);
  submitting    = signal(false);

  estudiantesEncontrados = signal<EstudianteOption[]>([]);
  estudianteSeleccionado = signal<EstudianteOption | null>(null);

  inscripcionCreada = signal<{ id_ins: number; id_us: number } | null>(null);

  readonly metodosPago = METODOS_PAGO;

  formBusqueda = this.fb.group({
    query: ['', [Validators.required, Validators.minLength(2)]],
  });

  formPrograma = this.fb.group({
    id_imp:      [null as number | null, [Validators.required]],
    id_plan:     [null as number | null],
    periodo:     [''],
    gestion:     [new Date().getFullYear().toString()],
    canal_venta: ['admin'],
    id_vendedor: [null as number | null],
  });

  formPago = this.fb.group({
    registrar_pago:    [false],
    metodo_pago:       ['efectivo' as MetodoPago],
    monto_pagado:      [null as number | null],
    id_us_cajero:      [null as number | null],
    nro_boleta:        [''],
    fecha_deposito:    [new Date().toISOString().split('T')[0]],
    observacion:       [''],
  });

  planSeleccionado = computed(() => {
    const id = this.formPrograma.get('id_plan')?.value;
    return id ? this.planes().find(p => p.id_plan === id) ?? null : null;
  });

  metodoPago = computed(() => this.formPago.get('metodo_pago')?.value as MetodoPago);
  registrarPago = computed(() => !!this.formPago.get('registrar_pago')?.value);

  ngOnInit(): void {
    this.http.get<{ data: ImparticionOption[] }>('/api/v1/imparticiones', {
      params: { pageSize: '200', pageIndex: '1', conInactivos: 'true' }
    }).subscribe({ next: r => this.imparticiones.set(r.data) });

    this.http.get<{ data: UsuarioOption[] }>('/api/v1/usuarios', {
      params: { pageSize: '200', pageIndex: '1' }
    }).subscribe({ next: r => this.usuarios.set(r.data) });
  }

  imparticionLabel(imp: ImparticionOption): string {
    const mat = imp.materia_nombre ?? `Programa ${imp.id_mat}`;
    const doc = imp.docente_nombre?.trim() || '';
    return `[${imp.periodo ?? '?'}] ${mat}${doc ? ' — ' + doc : ''}`;
  }

  buscarEstudiante(): void {
    const q = this.formBusqueda.get('query')?.value?.trim();
    if (!q) return;
    this.buscandoEst.set(true);
    this.http.get<{ data: EstudianteOption[] }>('/api/v1/usuarios-academicos', {
      params: { query: q, pageSize: '10', pageIndex: '1' }
    }).subscribe({
      next: r => {
        this.estudiantesEncontrados.set(r.data);
        this.buscandoEst.set(false);
      },
      error: () => this.buscandoEst.set(false),
    });
  }

  seleccionarEstudiante(est: EstudianteOption): void {
    this.estudianteSeleccionado.set(est);
    this.estudiantesEncontrados.set([]);
    this.paso.set(2);
  }

  onImparticionChange(event: Event): void {
    const idImp = Number((event.target as HTMLSelectElement).value);
    const imp = this.imparticiones().find(i => i.id_imp === idImp);
    if (!imp?.id_programa) return;
    this.loadingPlanes.set(true);
    this.http.get<{ data: PlanOption[] }>('/api/v1/planes-academicos', {
      params: { id_programa: imp.id_programa, pageSize: '50', pageIndex: '1' }
    }).subscribe({
      next: r => { this.planes.set(r.data); this.loadingPlanes.set(false); },
      error: () => this.loadingPlanes.set(false),
    });
  }

  irPaso3(): void {
    if (this.formPrograma.invalid) {
      this.formPrograma.markAllAsTouched();
      return;
    }
    this.paso.set(3);
  }

  confirmar(): void {
    const est = this.estudianteSeleccionado();
    if (!est) return;
    const prog = this.formPrograma.value;
    if (!prog.id_imp) return;

    this.submitting.set(true);

    const payload = {
      id_us:       est.id_us,
      id_imp:      prog.id_imp,
      id_plan:     prog.id_plan ?? null,
      periodo:     prog.periodo || null,
      gestion:     prog.gestion || null,
      canal_venta: prog.canal_venta || 'admin',
      id_vendedor: prog.id_vendedor ?? null,
    };

    this.inscripcionService.create(payload as any).subscribe({
      next: ins => {
        const idIns = (ins as any).id_ins;
        const idUs  = est.id_us;
        this.inscripcionCreada.set({ id_ins: idIns, id_us: idUs });

        if (this.registrarPago()) {
          this.registrarPagoInicial(idIns, idUs);
        } else {
          this.toast.success('Inscripción creada', `Inscripción #${idIns} registrada exitosamente.`);
          this.router.navigate(['/cenefco/venta-detalle', idIns]);
        }
      },
      error: () => {
        this.submitting.set(false);
        this.toast.error('Error', 'No se pudo crear la inscripción.');
      },
    });
  }

  private registrarPagoInicial(idIns: number, idUs: number): void {
    const pago = this.formPago.value;
    const monto = pago.monto_pagado;
    if (!monto || monto <= 0) {
      this.toast.success('Inscripción creada', `Inscripción #${idIns} registrada.`);
      this.router.navigate(['/cenefco/venta-detalle', idIns]);
      return;
    }

    this.ventaService.registrarPago({
      id_us:                idUs,
      id_ins:               idIns,
      monto_pagado:         monto,
      metodo_pago:          pago.metodo_pago as MetodoPago,
      id_us_cajero:         pago.id_us_cajero ?? null,
      nro_boleta_bancaria:  pago.nro_boleta || null,
      fecha_deposito:       pago.fecha_deposito || null,
      observacion_pago:     pago.observacion || null,
    }).subscribe({
      next: () => {
        this.toast.success('Venta registrada', `Inscripción #${idIns} con pago inicial registrado.`);
        this.router.navigate(['/cenefco/venta-detalle', idIns]);
      },
      error: () => {
        this.submitting.set(false);
        this.toast.warning('Inscripción creada', `La inscripción #${idIns} se creó, pero el pago inicial falló. Regístralo manualmente.`);
        this.router.navigate(['/cenefco/venta-detalle', idIns]);
      },
    });
  }
}
