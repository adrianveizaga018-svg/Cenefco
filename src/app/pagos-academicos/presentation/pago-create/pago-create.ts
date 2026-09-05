import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { PagoAcademicoService } from '../../application/services/pago-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { InscripcionDetalle } from '../../../inscripciones/domain/models/inscripcion.model';

interface UsuarioOption   { id_us: number; nombre: string; appaterno: string | null; ci: string | null; }
interface InscripcionOpt  { id_ins: number; id_us: number; id_imp: number; materia_nombre: string | null; imp_periodo: string | null; estudiante_nombre: string | null; }
interface FechaPagoOption { id_fechapago: number; nro_pago: string | null; monto_a_pagar: number | null; tipo_tramite: string | null; id_plan: number | null; }

@Component({
  selector: 'app-pago-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './pago-create.html',
})
export class PagoCreate implements OnInit {
  private service = inject(PagoAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);

  submitting       = signal(false);
  usuarios         = signal<UsuarioOption[]>([]);
  inscripciones    = signal<InscripcionOpt[]>([]);
  fechasPago       = signal<FechaPagoOption[]>([]);
  cargandoCuotas   = signal(false);
  todasFechasPago  = signal<FechaPagoOption[]>([]);

  private preselectedInsId: number | null = null;

  form = this.fb.group({
    id_us:              [null as number | null, [Validators.required]],
    id_ins:             [null as number | null],
    id_fechapago:       [null as number | null],
    monto_pagado:       [null as number | null, [Validators.required, Validators.min(0.01)]],
    nro_boleta_bancaria:[''],
    fecha_deposito:     [new Date().toISOString().split('T')[0]],
    nro_nit:            [''],
    nombre_nit:         [''],
    observacion_pago:   [''],
    estado:             [1],
  });

  ngOnInit(): void {
    const qp     = this.route.snapshot.queryParamMap;
    const qIdUs  = qp.get('id_us')  ? Number(qp.get('id_us'))  : null;
    const qIdIns = qp.get('id_ins') ? Number(qp.get('id_ins')) : null;
    this.preselectedInsId = qIdIns;

    this.http.get<{ data: UsuarioOption[] }>('/api/v1/usuarios-academicos', {
      params: { pageSize: '300', pageIndex: '1', conInactivos: 'true' }
    }).subscribe({
      next: r => {
        this.usuarios.set(r.data);
        if (qIdUs) {
          this.form.patchValue({ id_us: qIdUs });
          this.cargarInscripciones(qIdUs);
        }
      }
    });

    this.http.get<{ data: FechaPagoOption[] }>('/api/v1/fechas-pago', {
      params: { pageSize: '200', pageIndex: '1' }
    }).subscribe({
      next: r => {
        this.todasFechasPago.set(r.data);
        if (!qIdIns) this.fechasPago.set(r.data);
      }
    });
  }

  private cargarInscripciones(idUs: number): void {
    this.http.get<{ data: InscripcionOpt[] }>('/api/v1/inscripciones', {
      params: { id_us: idUs.toString(), pageSize: '50', conInactivos: 'true' }
    }).subscribe({
      next: r => {
        this.inscripciones.set(r.data);
        if (this.preselectedInsId) {
          this.form.patchValue({ id_ins: this.preselectedInsId });
          this.cargarCuotasPendientes(this.preselectedInsId);
        }
      }
    });
  }

  private cargarCuotasPendientes(idIns: number): void {
    this.cargandoCuotas.set(true);
    this.http.get<InscripcionDetalle>(`/api/v1/inscripciones/${idIns}`).subscribe({
      next: detalle => {
        const pagadas = new Set(
          detalle.pagos
            .filter(p => p.pago_estado === 1 && p.id_fechapago != null)
            .map(p => p.id_fechapago!)
        );

        const pendientes: FechaPagoOption[] = detalle.todas_cuotas
          .filter(c => !pagadas.has(c.id_fechapago))
          .map(c => ({
            id_fechapago:  c.id_fechapago,
            nro_pago:      c.nro_pago,
            monto_a_pagar: c.monto_a_pagar,
            tipo_tramite:  c.tipo_tramite,
            id_plan:       null,
          }));

        this.fechasPago.set(pendientes);
        this.cargandoCuotas.set(false);

        if (pendientes.length > 0 && !this.form.value.id_fechapago) {
          this.form.patchValue({
            id_fechapago: pendientes[0].id_fechapago,
            monto_pagado: pendientes[0].monto_a_pagar ?? null,
          });
        }
      },
      error: () => {
        this.fechasPago.set(this.todasFechasPago());
        this.cargandoCuotas.set(false);
      }
    });
  }

  usuarioLabel(u: UsuarioOption): string {
    return `${u.nombre} ${u.appaterno ?? ''}${u.ci ? ' — CI: ' + u.ci : ''}`.trim();
  }

  inscripcionLabel(i: InscripcionOpt): string {
    return `[Ins. #${i.id_ins}] ${i.materia_nombre ?? 'Sin materia'} (${i.imp_periodo ?? '—'})`;
  }

  fechaPagoLabel(f: FechaPagoOption): string {
    const cuota = f.nro_pago ? `Cuota ${f.nro_pago}` : 'Pago';
    const monto = f.monto_a_pagar != null ? ` — Bs. ${f.monto_a_pagar}` : '';
    const tipo  = f.tipo_tramite ? ` (${f.tipo_tramite})` : '';
    return `${cuota}${monto}${tipo}`;
  }

  onUsuarioChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    this.preselectedInsId = null;
    this.form.patchValue({ id_ins: null, id_fechapago: null });
    this.fechasPago.set(this.todasFechasPago());
    if (!id) { this.inscripciones.set([]); return; }
    this.cargarInscripciones(id);
  }

  onInscripcionChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    const ins = this.inscripciones().find(i => i.id_ins === id);
    this.form.patchValue({ id_fechapago: null, monto_pagado: null });
    if (ins) {
      this.form.patchValue({ id_us: ins.id_us });
      this.cargarCuotasPendientes(id);
    } else {
      this.fechasPago.set(this.todasFechasPago());
    }
  }

  onFechaPagoChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    const cuota = this.fechasPago().find(f => f.id_fechapago === id);
    if (cuota?.monto_a_pagar != null) {
      this.form.patchValue({ monto_pagado: cuota.monto_a_pagar });
    }
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Registrado!', 'Pago registrado correctamente');
        if (this.preselectedInsId) {
          this.router.navigate(['/cenefco/inscripcion-detail', this.preselectedInsId]);
        } else {
          this.router.navigate(['/cenefco/pagos-academicos']);
        }
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err));
        this.submitting.set(false);
      },
    });
  }
}
