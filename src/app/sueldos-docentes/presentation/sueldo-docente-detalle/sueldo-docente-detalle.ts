import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SueldoDocenteService } from '../../application/services/sueldo-docente.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { SueldoDocenteDetalle, PagoSueldo } from '../../domain/models/sueldo-docente.model';

export interface CuotaPreview {
  monto: number;
  fecha: string;
  nro:   string;
}

@Component({
  selector: 'app-sueldo-docente-detalle',
  imports: [NgIcon, RouterLink, DecimalPipe, FormsModule, PageTitle],
  templateUrl: './sueldo-docente-detalle.html',
})
export class SueldoDocenteDetalleComponent implements OnInit {
  private service = inject(SueldoDocenteService);
  private route   = inject(ActivatedRoute);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  sueldo       = signal<SueldoDocenteDetalle | null>(null);
  loading      = signal(true);
  hasError     = signal(false);
  eliminandoId = signal<number | null>(null);

  showPagoModal = signal(false);
  savingPago    = signal(false);

  tipoPago: 'unico' | 'cuotas' = 'unico';

  pagoForm = {
    monto_pagado:    null as number | null,
    fecha_pago:      new Date().toISOString().slice(0, 10),
    nro_comprobante: '',
    observacion:     '',
  };

  archivoComprobante: File | null = null;
  archivoNombre = '';

  cuotasForm = {
    n_cuotas:    3,
    monto_cuota: null as number | null,
    fecha_inicio: new Date().toISOString().slice(0, 10),
    intervalo:   30,
    nro_base:    '',
    observacion: '',
  };

  cuotasPreview: CuotaPreview[] = [];

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.cargar(id);
  }

  cargar(id: number): void {
    this.loading.set(true);
    this.service.getById(id).subscribe({
      next: s => { this.sueldo.set(s); this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.hasError.set(true); this.loading.set(false); this.cdr.detectChanges(); },
    });
  }

  abrirModal(): void {
    this.tipoPago = 'unico';
    this.pagoForm = {
      monto_pagado: null,
      fecha_pago:   new Date().toISOString().slice(0, 10),
      nro_comprobante: '',
      observacion:  '',
    };
    this.archivoComprobante = null;
    this.archivoNombre      = '';
    this.cuotasForm = {
      n_cuotas:    3,
      monto_cuota: null,
      fecha_inicio: new Date().toISOString().slice(0, 10),
      intervalo:   30,
      nro_base:    '',
      observacion: '',
    };
    this.cuotasPreview = [];
    this.showPagoModal.set(true);
  }

  cerrarModal(): void { this.showPagoModal.set(false); }

  setTipoPago(tipo: 'unico' | 'cuotas'): void {
    this.tipoPago = tipo;
    if (tipo === 'cuotas') this.calcularCuotas();
    this.cdr.detectChanges();
  }

  usarSaldoTotal(): void {
    const saldo = this.sueldo()?.saldo_pendiente ?? 0;
    this.pagoForm.monto_pagado = saldo;
    this.cdr.detectChanges();
  }

  calcularCuotas(): void {
    const saldo = this.sueldo()?.saldo_pendiente ?? 0;
    const n     = this.cuotasForm.n_cuotas;
    if (!n || n <= 0) { this.cuotasPreview = []; this.cdr.detectChanges(); return; }

    const monto = this.cuotasForm.monto_cuota != null
      ? this.cuotasForm.monto_cuota
      : Math.round((saldo / n) * 100) / 100;

    const base = new Date(this.cuotasForm.fecha_inicio + 'T12:00:00');

    this.cuotasPreview = Array.from({ length: n }, (_, i) => {
      const f = new Date(base);
      f.setDate(f.getDate() + i * (this.cuotasForm.intervalo || 30));
      const nro = this.cuotasForm.nro_base
        ? `${this.cuotasForm.nro_base}-${i + 1}`
        : '';
      return { monto, fecha: f.toISOString().slice(0, 10), nro };
    });
    this.cdr.detectChanges();
  }

  get totalCuotas(): number {
    return this.cuotasPreview.reduce((s, c) => s + c.monto, 0);
  }

  guardarPago(): void {
    const s = this.sueldo();
    if (!s) return;

    if (this.tipoPago === 'cuotas') {
      this.guardarCuotas(s);
    } else {
      this.guardarUnico(s);
    }
  }

  private guardarUnico(s: SueldoDocenteDetalle): void {
    if (!this.pagoForm.monto_pagado || !this.pagoForm.fecha_pago) {
      this.toast.error('Validación', 'Monto y fecha son obligatorios.');
      return;
    }
    if (this.pagoForm.monto_pagado <= 0) {
      this.toast.error('Validación', 'El monto debe ser mayor a 0.');
      return;
    }
    this.savingPago.set(true);
    this.service.addPago(s.id, { ...this.pagoForm, archivo: this.archivoComprobante }).subscribe({
      next: pago => {
        const monto = +(pago.monto_pagado);
        this.sueldo.update(sd => sd ? ({
          ...sd,
          pagos:           [pago, ...sd.pagos],
          total_pagado:    sd.total_pagado + monto,
          saldo_pendiente: Math.max(0, sd.saldo_pendiente - monto),
          estado_pago:     this.calcEstado(sd.total_pagado + monto, sd.monto_total),
        }) : sd);
        this.savingPago.set(false);
        this.showPagoModal.set(false);
        this.toast.success('Pago registrado', `Bs. ${monto.toFixed(2)} agregado correctamente.`);
        this.cdr.detectChanges();
      },
      error: () => {
        this.savingPago.set(false);
        this.toast.error('Error', 'No se pudo registrar el pago.');
      },
    });
  }

  private guardarCuotas(s: SueldoDocenteDetalle): void {
    if (this.cuotasPreview.length === 0) {
      this.toast.error('Validación', 'Configure las cuotas antes de guardar.');
      return;
    }
    if (this.cuotasPreview.some(c => c.monto <= 0)) {
      this.toast.error('Validación', 'El monto de cada cuota debe ser mayor a 0.');
      return;
    }
    this.savingPago.set(true);
    const cuotas = this.cuotasPreview.map(c => ({
      monto_pagado:    c.monto,
      fecha_pago:      c.fecha,
      nro_comprobante: c.nro,
      observacion:     this.cuotasForm.observacion,
    }));
    this.service.addPagoLote(s.id, cuotas).subscribe({
      next: pagos => {
        const totalNuevo = pagos.reduce((acc, p) => acc + +(p.monto_pagado), 0);
        this.sueldo.update(sd => sd ? ({
          ...sd,
          pagos:           [...pagos, ...sd.pagos],
          total_pagado:    sd.total_pagado + totalNuevo,
          saldo_pendiente: Math.max(0, sd.saldo_pendiente - totalNuevo),
          estado_pago:     this.calcEstado(sd.total_pagado + totalNuevo, sd.monto_total),
        }) : sd);
        this.savingPago.set(false);
        this.showPagoModal.set(false);
        this.toast.success('Cuotas registradas', `${pagos.length} cuota(s) por Bs. ${totalNuevo.toFixed(2)} registradas.`);
        this.cdr.detectChanges();
      },
      error: () => {
        this.savingPago.set(false);
        this.toast.error('Error', 'No se pudieron registrar las cuotas.');
      },
    });
  }

  eliminarPago(pago: PagoSueldo): void {
    const s = this.sueldo();
    if (!s) return;
    this.eliminandoId.set(pago.id);
    this.service.deletePago(s.id, pago.id).subscribe({
      next: () => {
        const monto = +(pago.monto_pagado);
        this.sueldo.update(sd => sd ? ({
          ...sd,
          pagos:           sd.pagos.filter(p => p.id !== pago.id),
          total_pagado:    Math.max(0, sd.total_pagado - monto),
          saldo_pendiente: sd.saldo_pendiente + monto,
          estado_pago:     this.calcEstado(Math.max(0, sd.total_pagado - monto), sd.monto_total),
        }) : sd);
        this.eliminandoId.set(null);
        this.toast.success('Eliminado', 'Pago eliminado correctamente.');
        this.cdr.detectChanges();
      },
      error: () => {
        this.eliminandoId.set(null);
        this.toast.error('Error', 'No se pudo eliminar el pago.');
      },
    });
  }

  onArchivoChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0] ?? null;
    this.archivoComprobante = file;
    this.archivoNombre      = file ? file.name : '';
    this.cdr.detectChanges();
  }

  quitarArchivo(input: HTMLInputElement): void {
    this.archivoComprobante = null;
    this.archivoNombre      = '';
    input.value             = '';
    this.cdr.detectChanges();
  }

  private calcEstado(pagado: number, total: number): 'pagado' | 'parcial' | 'pendiente' {
    if (total > 0 && pagado >= total) return 'pagado';
    if (pagado > 0) return 'parcial';
    return 'pendiente';
  }

  porcentaje(): number {
    const s = this.sueldo();
    if (!s || s.monto_total === 0) return 0;
    return Math.min(100, Math.round((s.total_pagado / s.monto_total) * 100));
  }

  barraClass(): string {
    const p = this.porcentaje();
    if (p >= 100) return 'bg-success';
    if (p > 0)    return 'bg-warning';
    return 'bg-danger';
  }

  badgeClass(estado: string): string {
    return ({
      pagado:    'bg-success/10 text-success',
      parcial:   'bg-warning/10 text-warning',
      pendiente: 'bg-danger/10 text-danger',
    } as Record<string, string>)[estado] ?? 'bg-default-100 text-default-500';
  }

  badgeLabel(estado: string): string {
    return ({ pagado: 'Pagado', parcial: 'Pago Parcial', pendiente: 'Pendiente' } as Record<string, string>)[estado] ?? estado;
  }

  iniciales(nombre: string | null): string {
    if (!nombre) return '?';
    return nombre.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
  }
}
