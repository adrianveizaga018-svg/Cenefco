import { Component, inject, signal } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe, NgStyle, UpperCasePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { VentaService } from '../../application/services/venta.service';
import { VentaDetalle as VentaDetalleModel, type MetodoPago, type PagoDetalle } from '../../domain/models/venta.model';
import { ReglamentoPrograma } from '../../domain/models/reglamento.model';
import { ToastService } from '../../../common/application/services/toast.service';

type S  = { type: 'loading' } | { type: 'success'; data: VentaDetalleModel } | { type: 'error' };
type SR = { type: 'loading' } | { type: 'success'; data: ReglamentoPrograma } | { type: 'idle' };
const L: S  = { type: 'loading' };
const E: S  = { type: 'error' };

export interface SeccionReglamento {
  campo:  keyof ReglamentoPrograma;
  titulo: string;
  icono:  string;
  color:  'azul' | 'verde';
}

const METODOS_PAGO: { value: MetodoPago; label: string }[] = [
  { value: 'efectivo',          label: 'Efectivo' },
  { value: 'deposito_bancario', label: 'Depósito Bancario' },
  { value: 'qr',                label: 'QR' },
];

@Component({
  selector: 'app-venta-detalle',
  imports: [NgIcon, RouterLink, PageTitle, DecimalPipe, NgStyle, UpperCasePipe, FormsModule],
  templateUrl: './venta-detalle.html',
})
export class VentaDetalle {
  private service = inject(VentaService);
  private route   = inject(ActivatedRoute);
  private toast   = inject(ToastService);

  readonly fechaHoy = new Date().toLocaleDateString('es-BO', {
    day: '2-digit', month: '2-digit', year: 'numeric',
  });

  readonly secciones: SeccionReglamento[] = [
    { campo: 'reglas_asistencia',  titulo: 'Asistencia y Puntualidad',            icono: '📋', color: 'azul'  },
    { campo: 'reglas_evaluacion',  titulo: 'Evaluaciones y Trabajos',             icono: '📝', color: 'azul'  },
    { campo: 'reglas_pagos',       titulo: 'Compromisos de Pago',                 icono: '💳', color: 'azul'  },
    { campo: 'reglas_conducta',    titulo: 'Convivencia y Respeto',               icono: '🤝', color: 'azul'  },
    { campo: 'reglas_plataformas', titulo: 'Plataformas Digitales (Zoom/WhatsApp)', icono: '💻', color: 'azul'  },
    { campo: 'reglas_derechos',    titulo: 'Derechos del Estudiante',             icono: '✅', color: 'verde' },
  ];

  readonly metodosPago = METODOS_PAGO;

  private ventaId = signal<number | null>(null);
  private refreshCounter = signal(0);

  private state = toSignal(
    this.route.paramMap.pipe(
      switchMap(params => {
        const id = Number(params.get('id'));
        this.ventaId.set(id);
        return this.service.getById(id).pipe(
          map(d => ({ type: 'success', data: d } as S)),
          startWith(L),
          catchError(() => of(E)),
        );
      }),
      startWith(L),
    ),
    { requireSync: true }
  );

  get venta()     { const s = this.state(); return s.type === 'success' ? s.data : null; }
  get isLoading() { return this.state().type === 'loading'; }
  get hasError()  { return this.state().type === 'error'; }

  descargandoPdf = signal(false);

  descargarPdf(): void {
    const v = this.venta;
    if (!v) return;
    this.descargandoPdf.set(true);
    this.service.descargarPdf(v.id_ins).subscribe({
      next: blob => {
        this.descargandoPdf.set(false);
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `comprobante-${v.id_ins}.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      },
      error: () => {
        this.descargandoPdf.set(false);
        this.toast.error('Error', 'No se pudo generar el comprobante PDF.');
      },
    });
  }

  showPago     = signal(false);
  pagoMonto    = signal<number | null>(null);
  pagoMetodo   = signal<MetodoPago>('efectivo');
  pagoBoleta   = signal('');
  pagoFecha    = signal(new Date().toISOString().split('T')[0]);
  pagoObs      = signal('');
  pagoFechapago = signal<number | null>(null);
  guardandoPago = signal(false);

  abrirPago(cuota?: PagoDetalle): void {
    this.pagoFechapago.set(null);
    this.pagoMonto.set(null);
    if (cuota) {
      this.pagoFechapago.set(cuota.id_pago ?? null);
      this.pagoMonto.set(cuota.monto_a_pagar ? parseFloat(cuota.monto_a_pagar) : null);
    }
    this.pagoMetodo.set('efectivo');
    this.pagoBoleta.set('');
    this.pagoFecha.set(new Date().toISOString().split('T')[0]);
    this.pagoObs.set('');
    this.showPago.set(true);
  }

  cerrarPago(): void { this.showPago.set(false); }

  guardarPago(): void {
    const v = this.venta;
    const monto = this.pagoMonto();
    if (!v || !monto || monto <= 0) {
      this.toast.error('Error', 'Ingresa un monto válido.');
      return;
    }

    this.guardandoPago.set(true);
    this.service.registrarPago({
      id_us:               v.id_us,
      id_ins:              v.id_ins,
      id_fechapago:        this.pagoFechapago(),
      monto_pagado:        monto,
      metodo_pago:         this.pagoMetodo(),
      nro_boleta_bancaria: this.pagoBoleta() || null,
      fecha_deposito:      this.pagoFecha() || null,
      observacion_pago:    this.pagoObs() || null,
    }).subscribe({
      next: () => {
        this.guardandoPago.set(false);
        this.showPago.set(false);
        this.toast.success('Pago registrado', `Bs. ${monto} registrado correctamente.`);
        window.location.reload();
      },
      error: (err: any) => {
        this.guardandoPago.set(false);
        const msg = err?.error?.message ?? 'No se pudo registrar el pago.';
        this.toast.error('Error', msg);
      },
    });
  }

  metodoPagoLabel(m: MetodoPago | null): string {
    return METODOS_PAGO.find(x => x.value === m)?.label ?? (m ?? '—');
  }

  metodoPagoBadge(m: MetodoPago | null): string {
    return ({
      efectivo:          'bg-success/10 text-success',
      deposito_bancario: 'bg-primary/10 text-primary',
      qr:                'bg-violet-100 text-violet-700',
      pago_online:       'bg-warning/10 text-warning',
    } as Record<string, string>)[m ?? ''] ?? 'bg-default-100 text-default-500';
  }

  reglamento       = signal<ReglamentoPrograma | null>(null);
  reglamentoLoading = signal(false);
  showEditor       = signal(false);
  guardando        = signal(false);

  draft = signal<Partial<ReglamentoPrograma>>({});

  cargarReglamento(): void {
    const v = this.venta;
    if (!v || this.reglamento()) return;
    const idRef = v.id_programa ?? v.id_imp;
    if (!idRef) return;
    this.reglamentoLoading.set(true);
    this.service.getReglamento(idRef).subscribe({
      next: r => { this.reglamento.set(r); this.reglamentoLoading.set(false); },
      error: () => this.reglamentoLoading.set(false),
    });
  }

  imprimir(): void {
    if (this.reglamento()) { window.print(); return; }
    const v = this.venta;
    const idRef = v ? (v.id_programa ?? v.id_imp) : null;
    if (!idRef) { window.print(); return; }
    this.reglamentoLoading.set(true);
    this.service.getReglamento(idRef).subscribe({
      next: r => { this.reglamento.set(r); this.reglamentoLoading.set(false); window.print(); },
      error: () => { this.reglamentoLoading.set(false); window.print(); },
    });
  }

  abrirEditor(): void {
    const r = this.reglamento();
    if (!r) return;
    this.draft.set({
      bienvenida:         r.bienvenida         ?? '',
      reglas_asistencia:  r.reglas_asistencia  ?? '',
      reglas_evaluacion:  r.reglas_evaluacion  ?? '',
      reglas_pagos:       r.reglas_pagos       ?? '',
      reglas_conducta:    r.reglas_conducta    ?? '',
      reglas_plataformas: r.reglas_plataformas ?? '',
      reglas_derechos:    r.reglas_derechos    ?? '',
    });
    this.showEditor.set(true);
  }

  cerrarEditor(): void { this.showEditor.set(false); }

  getDraft(campo: string): string {
    return (this.draft() as Record<string, string>)[campo] ?? '';
  }

  setDraft(campo: string, value: string): void {
    this.draft.update(d => ({ ...d, [campo]: value }));
  }

  guardar(): void {
    const v = this.venta;
    if (!v) return;
    const idRef = v.id_programa ?? v.id_imp;
    if (!idRef) return;
    this.guardando.set(true);
    this.service.saveReglamento(idRef, this.draft()).subscribe({
      next: r => {
        this.reglamento.set(r);
        this.guardando.set(false);
        this.showEditor.set(false);
        this.toast.success('Guardado', 'Reglamento actualizado correctamente.');
      },
      error: () => {
        this.guardando.set(false);
        this.toast.error('Error', 'No se pudo guardar el reglamento.');
      },
    });
  }

  parseItems(texto: string | null | undefined): string[] {
    if (!texto) return [];
    return String(texto).split('\n').map(l => l.trim()).filter(Boolean);
  }

  getSeccionItems(campo: keyof ReglamentoPrograma): string[] {
    const r = this.reglamento();
    return this.parseItems(r ? String(r[campo] ?? '') : null);
  }

  bienvenidaTexto(nombre: string, programa: string): string {
    const r = this.reglamento();
    if (r?.bienvenida) return r.bienvenida.replace('{nombre}', nombre).replace('{programa}', programa);
    return `Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al programa "${programa}". Estamos comprometidos a brindarle una formación académica de calidad, a través de docentes altamente calificados y metodologías actualizadas.`;
  }

  porcentaje(): number {
    const v = this.venta;
    if (!v || v.total_a_pagar === 0) return 0;
    return Math.min(100, Math.round((v.total_pagado / v.total_a_pagar) * 100));
  }

  barraClass(): string {
    const p = this.porcentaje();
    if (p >= 100) return 'bg-success';
    if (p > 0)    return 'bg-warning';
    return 'bg-danger';
  }

  badgeClass(estado: string): string {
    return ({
      pagado:   'bg-success/10 text-success',
      parcial:  'bg-warning/10 text-warning',
      pendiente:'bg-danger/10 text-danger',
    } as Record<string, string>)[estado] ?? 'bg-default-100 text-default-500';
  }

  badgeLabel(estado: string): string {
    return ({ pagado: 'Pagado', parcial: 'Pago Parcial', pendiente: 'Pendiente' } as Record<string, string>)[estado] ?? estado;
  }

  estadoStyle(): Record<string, string> {
    const v = this.venta;
    if (!v) return {};
    if (v.estado_pago === 'pagado')   return { background: '#d4edda', color: '#155724', border: '1px solid #c3e6cb' };
    if (v.estado_pago === 'parcial')  return { background: '#fff3cd', color: '#856404', border: '1px solid #ffc107' };
    return { background: '#f8d7da', color: '#721c24', border: '1px solid #f5c6cb' };
  }

  selloStyle(): Record<string, string> {
    const v = this.venta;
    if (!v) return {};
    if (v.estado_pago === 'pagado')  return { background: '#d4edda', color: '#155724', border: '2px solid #28a745' };
    if (v.estado_pago === 'parcial') return { background: '#fff3cd', color: '#856404', border: '2px solid #ffc107' };
    return { background: '#f8d7da', color: '#721c24', border: '2px solid #dc3545' };
  }
}
