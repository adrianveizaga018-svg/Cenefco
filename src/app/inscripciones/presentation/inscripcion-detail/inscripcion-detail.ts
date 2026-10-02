import { Component, inject, signal, computed, OnInit, OnDestroy } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe, SlicePipe, TitleCasePipe, UpperCasePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import Swal from 'sweetalert2';
import { InscripcionService } from '../../application/services/inscripcion.service';
import { InscripcionDetalle, type CuotaItem, type PagoItem } from '../../domain/models/inscripcion.model';
import { ReglamentoPrograma } from '../../../ventas/domain/models/reglamento.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { TipoBancoService } from '../../../tipos-banco/application/services/tipo-banco.service';
import { TipoBanco } from '../../../tipos-banco/domain/models/tipo-banco.model';
import { EnvioCertificadoService } from '../../../envios-certificado/application/services/envio-certificado.service';
import { EnvioCertificado } from '../../../envios-certificado/domain/models/envio-certificado.model';
import { CompromisoCobroService } from '../../../compromisos-cobro/application/services/compromiso-cobro.service';
import { CertificadoService } from '../../../certificados/application/services/certificado.service';
import { CajaService } from '../../../caja/application/services/caja.service';
import {
  CompromisoCobroLog,
  ESTADO_COMPROMISO_CLASES,
  ESTADO_COMPROMISO_LABELS,
  EstadoCompromisoCobro,
  MOTIVOS_REPROGRAMACION,
  MotivoReprogramacion,
} from '../../../compromisos-cobro/domain/models/compromiso-cobro.model';

export interface SeccionReglamento {
  campo: keyof ReglamentoPrograma;
  titulo: string;
  icono: string;
  color: 'azul' | 'verde';
}

@Component({
  selector: 'app-inscripcion-detail',
  imports: [NgIcon, PageTitle, RouterLink, DecimalPipe, SlicePipe, TitleCasePipe, FormsModule, UpperCasePipe],
  templateUrl: './inscripcion-detail.html',
})
export class InscripcionDetail implements OnInit, OnDestroy {
  private service = inject(InscripcionService);
  private route = inject(ActivatedRoute);
  private toast = inject(ToastService);
  private auth = inject(AuthService);
  private tipoBancoService = inject(TipoBancoService);
  private envioCertService = inject(EnvioCertificadoService);
  private compromisoCobroService = inject(CompromisoCobroService);
  private certSvc  = inject(CertificadoService);
  private cajaSvc  = inject(CajaService);

  canAprobar = computed(() => {
    const user = this.auth.currentUser();
    const role = (user as any)?.role_nombre ?? (user as any)?.rolNombre ?? '';
    return role.toLowerCase() === 'admin';
  });

  get puedeCrearCompromiso(): boolean { return this.auth.hasPermission('compromisos-cobro.crear'); }
  get puedeEditarCompromiso(): boolean { return this.auth.hasPermission('compromisos-cobro.editar'); }

  readonly fechaHoy = new Date().toLocaleDateString('es-BO', {
    day: '2-digit', month: '2-digit', year: 'numeric',
  });

  readonly hoyIsoAttr = new Date().toISOString().split('T')[0];

  readonly secciones: SeccionReglamento[] = [
    { campo: 'reglas_asistencia', titulo: 'Asistencia y Puntualidad', icono: '📋', color: 'azul' },
    { campo: 'reglas_evaluacion', titulo: 'Evaluaciones y Trabajos', icono: '📝', color: 'azul' },
    { campo: 'reglas_pagos', titulo: 'Compromisos de Pago', icono: '💳', color: 'azul' },
    { campo: 'reglas_conducta', titulo: 'Convivencia y Respeto', icono: '🤝', color: 'azul' },
    { campo: 'reglas_plataformas', titulo: 'Plataformas Digitales (Zoom/WhatsApp)', icono: '💻', color: 'azul' },
    { campo: 'reglas_derechos', titulo: 'Derechos del Estudiante', icono: '✅', color: 'verde' },
  ];

  loading = signal(true);
  error = signal(false);
  forbidden = signal(false);
  detalle = signal<InscripcionDetalle | null>(null);

  id = Number(this.route.snapshot.paramMap.get('id'));

  showAnticipo = signal(false);
  anticipoMonto = signal<number | null>(null);
  anticipoBoleta = signal('');
  anticipoFecha = signal('');
  anticipoObs = signal('');
  guardandoAnticipo = signal(false);

  showPago = signal(false);
  pagoEditandoId = signal<number | null>(null);
  pagoMontoOriginal = signal<number | null>(null);
  pagoIdFechapago = signal<number | null>(null);
  pagoMonto = signal<number | null>(null);
  pagoMetodo = signal<string>('efectivo');
  pagoBoleta = signal('');
  pagoTipoBanco = signal<number | null>(null);
  pagoFecha = signal('');
  pagoObs = signal('');
  pagoArchivo = signal<File | null>(null);
  pagoSinComprobante = signal(false);
  guardandoPago = signal(false);
  tiposBanco = signal<TipoBanco[]>([]);

  pagoAplicaDescuento = signal(false);
  pagoMontoDescuento = signal<number | null>(null);
  pagoMotivoDescuento = signal('');

  enviosCertificado = signal<EnvioCertificado[]>([]);
  showEnvioCertificado = signal(false);
  envioCiudad = signal('');
  envioFecha = signal(new Date().toISOString().split('T')[0]);
  envioArchivo = signal<File | null>(null);
  envioAclaraciones = signal('');
  envioCosto = signal<number | null>(null);
  guardandoEnvio = signal(false);
  eliminandoEnvioId = signal<number | null>(null);

  readonly metodosPago = [
    { value: 'efectivo', label: 'Efectivo' },
    { value: 'deposito_bancario', label: 'Depósito bancario' },
    { value: 'qr', label: 'QR' },
  ];

  readonly motivosDescuentoSugeridos = [
    'Cliente antiguo',
    'Pronto pago',
    'Convenio institucional',
    'Pago al contado',
    'Grupo familiar',
  ];

  // ── Baja y Reactivación ───────────────────────────────────────────────
  procesandoBaja       = signal(false);
  procesandoReactivar  = signal(false);

  // ── Cambio de programa / Transferencia ────────────────────────────────
  showTransferencia     = signal(false);
  transferProgramas     = signal<any[]>([]);
  transferBuscando      = signal(false);
  transferQuery         = signal('');
  transferProgSel       = signal<any | null>(null);
  transferImpSel        = signal<any | null>(null);
  transferPlanSel       = signal<any | null>(null);
  transferMotivo        = signal('');
  transferPreview       = signal<import('../../domain/models/inscripcion.model').PreviewTransferencia | null>(null);
  transferCargandoPreview = signal(false);
  transferProcesando    = signal(false);

  // ── Certificado individual ─────────────────────────────────────────────
  emitiendoCertificado  = signal(false);

  showDevolucion = signal(false);
  devolucionMonto = signal<number | null>(null);
  devolucionMotivo = signal('');
  devolucionDocUrl = signal('');
  subiendoDoc = signal(false);
  guardandoDevolucion = signal(false);

  readonly motivosReprogramacion = MOTIVOS_REPROGRAMACION;

  showCompromiso = signal(false);
  compromisoFecha = signal('');
  compromisoHora = signal('');
  compromisoMonto = signal<number | null>(null);
  compromisoObs = signal('');
  guardandoCompromiso = signal(false);

  showReprogramarCompromiso = signal(false);
  reprogramarCompromisoId = signal<number | null>(null);
  reprogramarFecha = signal('');
  reprogramarHora = signal('');
  reprogramarMotivo = signal<MotivoReprogramacion>('pidio_mas_tiempo');
  reprogramarObs = signal('');
  guardandoReprogramar = signal(false);

  showHistorialCompromiso = signal(false);
  cargandoHistorialCompromiso = signal(false);
  historialCompromiso = signal<CompromisoCobroLog[]>([]);
  private historialCompromisoIdCargado: number | null = null;

  private pollInterval: ReturnType<typeof setInterval> | null = null;
  private readonly pollIntervalMs = 15000;
  refrescandoPagos = signal(false);

  constructor() {
    this.route.paramMap.subscribe(params => {
      this.id = Number(params.get('id'));
      this.cargar();
    });
    this.tipoBancoService.getActivos().subscribe({ next: r => this.tiposBanco.set(r), error: () => { } });
  }

  ngOnInit(): void {
    this.pollInterval = setInterval(() => this.refrescarPagosSilencioso(), this.pollIntervalMs);
  }

  ngOnDestroy(): void {
    if (this.pollInterval) clearInterval(this.pollInterval);
  }

  private refrescarPagosSilencioso(): void {
    if (!this.detalle() || this.error() || this.forbidden()) return;
    this.refrescandoPagos.set(true);
    this.service.getDetalle(this.id).subscribe({
      next: d => { this.detalle.set(d); this.refrescandoPagos.set(false); },
      error: () => this.refrescandoPagos.set(false),
    });
  }

  cargar(): void {
    this.loading.set(true);
    this.error.set(false);
    this.forbidden.set(false);
    this.detalle.set(null);
    this.service.getDetalle(this.id).subscribe({
      next: d => { this.detalle.set(d); this.loading.set(false); },
      error: (err: HttpErrorResponse) => {
        if (err.status === 403) this.forbidden.set(true);
        else this.error.set(true);
        this.loading.set(false);
      },
    });
    this.cargarEnviosCertificado();
  }

  cargarEnviosCertificado(): void {
    this.envioCertService.getByInscripcion(this.id).subscribe({
      next: r => this.enviosCertificado.set(r),
      error: () => { },
    });
  }

  toggleEnvioCertificado(): void {
    this.showEnvioCertificado.update(v => !v);
    if (this.showEnvioCertificado()) {
      this.envioCiudad.set('');
      this.envioFecha.set(new Date().toISOString().split('T')[0]);
      this.envioArchivo.set(null);
      this.envioAclaraciones.set('');
      this.envioCosto.set(null);
    }
  }

  onEnvioArchivoSeleccionado(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.envioArchivo.set(file);
  }

  guardarEnvioCertificado(): void {
    if (!this.envioCiudad().trim()) { this.toast.error('Error', 'Indique la ciudad de destino'); return; }
    if (!this.envioFecha()) { this.toast.error('Error', 'Indique la fecha de envío'); return; }
    if (!this.envioArchivo()) { this.toast.error('Error', 'Adjunte la imagen de la guía'); return; }

    this.guardandoEnvio.set(true);
    this.envioCertService.create({
      id_ins: this.id,
      ciudad_destino: this.envioCiudad().trim(),
      fecha_envio: this.envioFecha(),
      imagen_guia: this.envioArchivo()!,
      aclaraciones: this.envioAclaraciones().trim() || null,
      costo: this.envioCosto(),
    }).subscribe({
      next: () => {
        this.toast.success('Envío registrado', 'El envío del certificado fue guardado correctamente');
        this.guardandoEnvio.set(false);
        this.showEnvioCertificado.set(false);
        this.cargarEnviosCertificado();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar el envío'));
        this.guardandoEnvio.set(false);
      },
    });
  }

  eliminarEnvioCertificado(id: number): void {
    Swal.fire({
      title: '¿Eliminar registro de envío?', text: 'Esta acción no se puede deshacer.',
      icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.eliminandoEnvioId.set(id);
      this.envioCertService.delete(id).subscribe({
        next: () => {
          this.toast.success('Eliminado', 'El registro de envío fue eliminado');
          this.eliminandoEnvioId.set(null);
          this.cargarEnviosCertificado();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar'));
          this.eliminandoEnvioId.set(null);
        },
      });
    });
  }

  inicialesDocente(nombre: string): string {
    return nombre.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
  }

  porcentajePagado(): number {
    const r = this.detalle()?.resumen;
    if (!r || !r.total_plan) return 0;
    return Math.min(100, Math.round((r.total_pagado / r.total_plan) * 100));
  }

  private sinPagos(r: { cuotas_pagadas: number; total_anticipos: number; total_pagado: number }): boolean {
    return r.cuotas_pagadas === 0 && r.total_anticipos === 0 && r.total_pagado === 0;
  }

  estadoPagoLabel(): string {
    const r = this.detalle()?.resumen;
    if (!r) return '—';
    if (this.sinPagos(r)) return 'Sin pagos';
    if (r.cuotas_totales > 0) {
      if (r.cuotas_pagadas >= r.cuotas_totales) return 'Pagado completo';
      return `${r.cuotas_pagadas} de ${r.cuotas_totales} cuota(s)`;
    }
    return 'Con pagos registrados';
  }

  estadoPagoClass(): string {
    const r = this.detalle()?.resumen;
    if (!r) return 'text-danger';
    if (this.sinPagos(r)) return 'text-danger';
    if (r.cuotas_totales > 0 && r.cuotas_pagadas >= r.cuotas_totales) return 'text-success';
    return 'text-warning';
  }

  barraClass(): string {
    const pct = this.porcentajePagado();
    if (pct >= 100) return 'bg-success';
    if (pct > 0) return 'bg-warning';
    return 'bg-danger';
  }

  totalDescuentos(): number {
    const pagos = this.detalle()?.pagos ?? [];
    return pagos.reduce((sum, p) => sum + Number(p.monto_descuento ?? 0), 0);
  }

  porcentajePagadoCosto(): number {
    const d = this.detalle();
    const costo = d?.inscripcion?.curso_costo_monto;
    if (!d || !costo) return 0;
    return Math.min(100, Math.round((d.resumen.total_pagado / costo) * 100));
  }

  barraClassCosto(): string {
    const pct = this.porcentajePagadoCosto();
    if (pct >= 100) return 'bg-success';
    if (pct > 0) return 'bg-warning';
    return 'bg-danger';
  }

  costoTotalCurso(): number | null {
    const d = this.detalle();
    if (!d) return null;
    if (d.resumen.total_plan > 0) return d.resumen.total_plan;
    if (d.inscripcion.curso_costo_monto) return d.inscripcion.curso_costo_monto;
    return null;
  }

  pendienteCosto(): number {
    const d = this.detalle();
    const costo = d?.inscripcion?.curso_costo_monto;
    if (!d || !costo) return 0;
    return Math.max(0, costo - d.resumen.total_pagado);
  }

  montoMaximoPago(): number | null {
    const d = this.detalle();
    const total = this.costoTotalCurso();
    if (!d || total == null) return null;
    const yaPagadoSinEsteRegistro = d.resumen.total_pagado - (this.pagoMontoOriginal() ?? 0);
    return Math.max(0, total - yaPagadoSinEsteRegistro);
  }

  abrirPago(cuota?: CuotaItem): void {
    this.pagoEditandoId.set(null);
    this.pagoMontoOriginal.set(null);
    this.pagoIdFechapago.set(cuota?.id_fechapago ?? null);
    this.pagoMonto.set(cuota?.monto_a_pagar ?? null);
    this.pagoMetodo.set('efectivo');
    this.pagoBoleta.set('');
    this.pagoTipoBanco.set(null);
    this.pagoFecha.set(new Date().toISOString().split('T')[0]);
    this.pagoObs.set('');
    this.pagoArchivo.set(null);
    this.pagoSinComprobante.set(false);
    this.pagoAplicaDescuento.set(false);
    this.pagoMontoDescuento.set(null);
    this.pagoMotivoDescuento.set('');
    this.showPago.set(true);
  }

  abrirEditarPago(pago: PagoItem): void {
    this.pagoEditandoId.set(pago.id_pago);
    this.pagoMontoOriginal.set(pago.monto_pagado);
    this.pagoIdFechapago.set(pago.id_fechapago);
    this.pagoMonto.set(pago.monto_pagado);
    this.pagoMetodo.set(pago.metodo_pago ?? 'efectivo');
    this.pagoBoleta.set(pago.nro_boleta_bancaria ?? '');
    this.pagoTipoBanco.set(pago.tipo_banco_id ?? null);
    this.pagoFecha.set(pago.fecha_deposito ?? '');
    this.pagoObs.set(pago.observacion_pago ?? '');
    this.pagoArchivo.set(null);
    this.pagoSinComprobante.set(!pago.nro_boleta_bancaria && !pago.comprobante_archivo);
    this.pagoAplicaDescuento.set(!!pago.monto_descuento);
    this.pagoMontoDescuento.set(pago.monto_descuento ?? null);
    this.pagoMotivoDescuento.set(pago.motivo_descuento ?? '');
    this.showPago.set(true);
  }

  toggleDescuentoPago(): void {
    this.pagoAplicaDescuento.update(v => !v);
    if (!this.pagoAplicaDescuento()) {
      this.pagoMontoDescuento.set(null);
      this.pagoMotivoDescuento.set('');
    }
  }

  puedeEditarPago(pago: PagoItem): boolean {
    const userId = (this.auth.currentUser() as any)?.id;
    return (pago.estado_verificacion === 'pendiente' || pago.estado_verificacion === 'observado')
      && pago.id_us_cajero === userId;
  }

  cerrarPago(): void {
    this.showPago.set(false);
    this.pagoEditandoId.set(null);
    this.pagoMontoOriginal.set(null);
    this.pagoSinComprobante.set(false);
  }

  onPagoArchivoSeleccionado(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.pagoArchivo.set(file);
  }

  toggleSinComprobante(): void {
    this.pagoSinComprobante.update(v => !v);
    if (this.pagoSinComprobante()) {
      this.pagoBoleta.set('');
      this.pagoArchivo.set(null);
    }
  }

  guardarPago(): void {
    const monto = this.pagoMonto();
    if (!monto || monto <= 0) { this.toast.error('Error', 'Ingrese un monto válido'); return; }

    const max = this.montoMaximoPago();
    if (max != null && monto > max + 0.01) {
      this.toast.error('Monto excede el costo del curso', `El monto máximo permitido es Bs. ${max.toFixed(2)}`);
      return;
    }

    if (this.pagoSinComprobante() && !this.pagoTipoBanco()) {
      this.toast.error('Error', 'Seleccione el banco / canal ya que este pago no tiene comprobante');
      return;
    }
    if (!this.pagoSinComprobante() && !this.pagoEditandoId() && !this.pagoBoleta().trim()) { this.toast.error('Error', 'Ingrese el N° de boleta / referencia'); return; }
    if (!this.pagoSinComprobante() && !this.pagoEditandoId() && !this.pagoArchivo()) { this.toast.error('Error', 'Adjunte el comprobante de pago (PDF o imagen)'); return; }

    const montoDescuento = this.pagoAplicaDescuento() ? this.pagoMontoDescuento() : null;
    const motivoDescuento = this.pagoAplicaDescuento() ? this.pagoMotivoDescuento().trim() : null;
    if (this.pagoAplicaDescuento()) {
      if (!montoDescuento || montoDescuento <= 0) { this.toast.error('Error', 'Ingrese el monto del descuento'); return; }
      if (!motivoDescuento) { this.toast.error('Error', 'Indique el motivo del descuento'); return; }
    }

    const ins = this.detalle()?.inscripcion;
    if (!ins) return;

    const idEditando = this.pagoEditandoId();
    this.guardandoPago.set(true);

    const observacionFinal = this.pagoSinComprobante()
      ? ['Sin comprobante de pago', this.pagoObs().trim()].filter(Boolean).join(' — ')
      : (this.pagoObs() || null);

    if (idEditando) {
      this.service.editarPago(idEditando, {
        monto_pagado: monto,
        nro_boleta_bancaria: this.pagoBoleta() || null,
        tipo_banco_id: this.pagoTipoBanco(),
        fecha_deposito: this.pagoFecha() || null,
        observacion_pago: observacionFinal,
        comprobante_archivo: this.pagoArchivo(),
        monto_descuento: montoDescuento,
        motivo_descuento: motivoDescuento,
      }).subscribe({
        next: () => {
          this.toast.success('Pago actualizado', 'El pago fue editado correctamente');
          this.guardandoPago.set(false);
          this.showPago.set(false);
          this.pagoEditandoId.set(null);
          this.cargar();
        },
        error: (err) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo editar el pago'));
          this.guardandoPago.set(false);
        },
      });
      return;
    }

    this.service.registrarPago({
      id_ins: ins.id_ins,
      id_us: ins.id_us,
      id_fechapago: this.pagoIdFechapago(),
      monto_pagado: monto,
      metodo_pago: this.pagoMetodo(),
      nro_boleta_bancaria: this.pagoBoleta() || null,
      tipo_banco_id: this.pagoTipoBanco(),
      fecha_deposito: this.pagoFecha() || null,
      observacion_pago: observacionFinal,
      comprobante_archivo: this.pagoArchivo(),
      monto_descuento: montoDescuento,
      motivo_descuento: motivoDescuento,
    }).subscribe({
      next: () => {
        this.toast.success('Pago registrado', 'El pago fue guardado correctamente');
        this.guardandoPago.set(false);
        this.showPago.set(false);
        this.cargar();
      },
      error: (err) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar el pago'));
        this.guardandoPago.set(false);
      },
    });
  }

  verificarPago(pago: PagoItem): void {
    Swal.fire({
      title: '¿Verificar este pago?',
      text: 'Se marcará como verificado y ya no podrá editarse.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, verificar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.verificarPago(pago.id_pago).subscribe({
          next: () => { this.toast.success('Verificado', 'El pago fue verificado correctamente'); this.cargar(); },
          error: (err) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo verificar el pago')),
        });
      }
    });
  }

  observarPago(pago: PagoItem): void {
    Swal.fire({
      title: 'Observar este pago',
      text: 'Indica por qué el comprobante o el pago no es válido. El pago volverá a estado "pendiente" para que quien lo registró pueda corregirlo.',
      icon: 'warning',
      input: 'textarea',
      inputPlaceholder: 'Ej: el comprobante no coincide con el monto registrado...',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Observar',
      cancelButtonText: 'Cancelar',
      inputValidator: (value) => (!value || value.trim().length < 3) ? 'Escribe un motivo (mínimo 3 caracteres)' : undefined,
    }).then(r => {
      if (r.isConfirmed) {
        this.service.observarPago(pago.id_pago, r.value.trim()).subscribe({
          next: () => { this.toast.success('Observado', 'El pago fue marcado como observado'); this.cargar(); },
          error: (err) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo observar el pago')),
        });
      }
    });
  }

  toggleAnticipo(): void {
    this.showAnticipo.update(v => !v);
    if (!this.showAnticipo()) this.resetAnticipo();
  }

  resetAnticipo(): void {
    this.anticipoMonto.set(null);
    this.anticipoBoleta.set('');
    this.anticipoFecha.set('');
    this.anticipoObs.set('');
  }

  guardarAnticipo(): void {
    const monto = this.anticipoMonto();
    if (!monto || monto <= 0) { this.toast.error('Error', 'Ingrese un monto válido'); return; }

    this.guardandoAnticipo.set(true);
    this.service.registrarAnticipo(this.id, {
      monto_pagado: monto,
      nro_boleta_bancaria: this.anticipoBoleta() || undefined,
      fecha_deposito: this.anticipoFecha() || undefined,
      observacion_pago: this.anticipoObs() || undefined,
    }).subscribe({
      next: () => {
        this.toast.success('Anticipo registrado', 'El anticipo fue guardado correctamente');
        this.guardandoAnticipo.set(false);
        this.showAnticipo.set(false);
        this.resetAnticipo();
        this.cargar();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo registrar el anticipo');
        this.guardandoAnticipo.set(false);
      },
    });
  }

  toggleDevolucion(): void {
    this.showDevolucion.update(v => !v);
    if (!this.showDevolucion()) this.resetDevolucion();
  }

  resetDevolucion(): void {
    this.devolucionMonto.set(null);
    this.devolucionMotivo.set('');
    this.devolucionDocUrl.set('');
  }

  subirDocDevolucion(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.subiendoDoc.set(true);
    this.service.uploadDocumentoFile(file).subscribe({
      next: res => { this.devolucionDocUrl.set(res.url); this.subiendoDoc.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo subir el documento'); this.subiendoDoc.set(false); },
    });
  }

  quitarDocDevolucion(): void { this.devolucionDocUrl.set(''); }

  nombreArchivoDevolucion(): string {
    const url = this.devolucionDocUrl();
    return url ? url.split('/').pop() ?? url : '';
  }

  // ── BAJA Y REACTIVACIÓN ───────────────────────────────────────────────

  darDeBaja(): void {
    Swal.fire({
      title: '¿Dar de baja esta inscripción?',
      html: `<div class="text-left">
        <p class="text-sm text-gray-600 mb-3">Esta acción puede revertirse posteriormente desde el mismo detalle.</p>
        <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Motivo de la baja *</label>
        <textarea id="motivo-baja" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows="3"
          placeholder="Ej: Solicitud del estudiante, dificultades económicas..."></textarea>
      </div>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Dar de baja',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false,
      preConfirm: () => {
        const motivo = (document.getElementById('motivo-baja') as HTMLTextAreaElement)?.value?.trim();
        if (!motivo) { Swal.showValidationMessage('El motivo es requerido'); return false; }
        return motivo;
      },
    }).then(r => {
      if (!r.isConfirmed || !r.value) return;
      this.procesandoBaja.set(true);
      this.service.darDeBaja(this.id, r.value).subscribe({
        next: () => {
          this.toast.success('Dada de baja', 'La inscripción fue dada de baja correctamente.');
          this.procesandoBaja.set(false);
          this.cargar();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo dar de baja.'));
          this.procesandoBaja.set(false);
        },
      });
    });
  }

  reactivarInscripcion(): void {
    Swal.fire({
      title: '¿Reactivar esta inscripción?',
      text: 'El estudiante volverá a quedar activo en el programa.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, reactivar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.procesandoReactivar.set(true);
      this.service.reactivar(this.id).subscribe({
        next: () => {
          this.toast.success('Reactivada', 'La inscripción fue reactivada correctamente.');
          this.procesandoReactivar.set(false);
          this.cargar();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo reactivar.'));
          this.procesandoReactivar.set(false);
        },
      });
    });
  }

  // ── CAMBIO DE PROGRAMA ────────────────────────────────────────────────

  abrirTransferencia(): void {
    this.showTransferencia.set(true);
    this.transferQuery.set('');
    this.transferProgSel.set(null);
    this.transferImpSel.set(null);
    this.transferPlanSel.set(null);
    this.transferPreview.set(null);
    this.transferMotivo.set('');
    this.cajaSvc.buscarProgramas('').subscribe({
      next: progs => this.transferProgramas.set(progs),
      error: () => {},
    });
  }

  cerrarTransferencia(): void { this.showTransferencia.set(false); }

  buscarProgramasTransfer(): void {
    this.transferBuscando.set(true);
    this.cajaSvc.buscarProgramas(this.transferQuery()).subscribe({
      next: progs => { this.transferProgramas.set(progs); this.transferBuscando.set(false); },
      error: () => this.transferBuscando.set(false),
    });
  }

  seleccionarProgTransfer(prog: any): void {
    this.transferProgSel.set(prog);
    this.transferImpSel.set(null);
    this.transferPlanSel.set(null);
    this.transferPreview.set(null);
  }

  seleccionarImpTransfer(imp: any): void {
    this.transferImpSel.set(imp);
    this.transferPlanSel.set(null);
    this.transferPreview.set(null);
  }

  seleccionarPlanTransfer(plan: any): void {
    this.transferPlanSel.set(plan);
    this.transferPreview.set(null);
    // Cargar preview automáticamente
    this.transferCargandoPreview.set(true);
    this.service.previewTransferencia(this.id, plan.id_plan).subscribe({
      next: p => { this.transferPreview.set(p); this.transferCargandoPreview.set(false); },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo calcular la transferencia.'));
        this.transferCargandoPreview.set(false);
      },
    });
  }

  confirmarTransferencia(): void {
    const imp  = this.transferImpSel();
    const plan = this.transferPlanSel();
    if (!imp || !plan) return;
    const motivo = this.transferMotivo().trim() || 'Cambio de programa solicitado por el estudiante';

    this.transferProcesando.set(true);
    this.service.transferir(this.id, {
      id_imp_destino:  imp.id_imp,
      id_plan_destino: plan.id_plan,
      motivo,
    }).subscribe({
      next: (res) => {
        this.transferProcesando.set(false);
        this.showTransferencia.set(false);
        Swal.fire({
          icon: 'success',
          title: '¡Transferencia completada!',
          html: `<p class="text-sm">La inscripción fue transferida correctamente.<br>
                 Nueva inscripción: <strong>#${res.id_ins_destino}</strong></p>`,
          confirmButtonText: 'Ver nueva inscripción',
          showCancelButton: true,
          cancelButtonText: 'Cerrar',
        }).then(r2 => {
          if (r2.isConfirmed) {
            window.location.href = `/cenefco/inscripcion-detail/${res.id_ins_destino}`;
          } else {
            this.cargar(); // Recarga para mostrar estado baja
          }
        });
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo realizar la transferencia.'));
        this.transferProcesando.set(false);
      },
    });
  }

  // ── CERTIFICADO INDIVIDUAL ────────────────────────────────────────────

  emitirCertificadoIndividual(): void {
    const d = this.detalle();
    if (!d) return;
    const ins = d.inscripcion;

    // Verificar pago completo
    const resumen = d.resumen as any;
    const pendiente = resumen?.pendiente ?? resumen?.total_pendiente ?? null;
    const pagoCompleto = pendiente !== null ? pendiente <= 0 : false;

    if (!pagoCompleto) {
      Swal.fire({
        icon: 'warning',
        title: 'Pago incompleto',
        html: `<p class="text-sm">El estudiante tiene un saldo pendiente de <strong>Bs. ${Math.abs(pendiente ?? 0).toFixed(2)}</strong>.<br>
               Solo se emiten certificados a estudiantes con pago completo.</p>`,
        confirmButtonText: 'Entendido',
      });
      return;
    }

    // Cargar plantillas
    Swal.fire({ title: 'Cargando plantillas...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

    this.certSvc.getPlantillas({ soloActivos: true }).subscribe({
      next: (plantRes) => {
        Swal.close();
        if (!plantRes.data.length) {
          Swal.fire({
            icon: 'error',
            title: '🚫 Sin plantilla',
            html: '<p class="text-sm">No hay plantillas de certificado configuradas. Ve a <b>Certificados → Plantillas</b> y crea una.</p>',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#e74c3c',
          });
          return;
        }

        const opciones = plantRes.data.reduce<Record<number, string>>((acc, p) => {
          acc[p.id] = p.nombre ?? `Plantilla #${p.id}`;
          return acc;
        }, {});

        Swal.fire({
          icon: 'question',
          title: '¿Emitir certificado?',
          html: `<p class="text-sm text-gray-700 mb-3">Se generará un certificado para <strong>${ins.estudiante_nombre}</strong>.</p>
                 <div class="text-left">
                   <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Plantilla</label>
                   <select id="plantilla-ind" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                     ${Object.entries(opciones).map(([id, nombre]) => `<option value="${id}">${nombre}</option>`).join('')}
                   </select>
                 </div>`,
          showCancelButton: true,
          confirmButtonText: '🎓 Emitir',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#2563eb',
          allowOutsideClick: false,
          preConfirm: () => {
            const sel = document.getElementById('plantilla-ind') as HTMLSelectElement;
            return sel ? Number(sel.value) : null;
          },
        }).then(result => {
          if (!result.isConfirmed || !result.value) return;

          this.emitiendoCertificado.set(true);
          Swal.fire({ title: 'Generando...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

          this.certSvc.generarLote(ins.id_imp as number, result.value).subscribe({
            next: (res: any) => {
              this.emitiendoCertificado.set(false);
              Swal.fire({
                icon: 'success',
                title: '¡Certificado emitido!',
                html: `<p class="text-sm">El certificado de <strong>${ins.estudiante_nombre}</strong> fue generado correctamente.</p>`,
                confirmButtonText: 'Ver certificados',
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
              }).then(r2 => {
                if (r2.isConfirmed) {
                  window.open(`/cenefco/certificados?imparte_id=${ins.id_imp}`, '_blank');
                }
              });
            },
            error: (err: any) => {
              this.emitiendoCertificado.set(false);
              Swal.fire('Error', err?.error?.message ?? 'No se pudo generar el certificado.', 'error');
            },
          });
        });
      },
      error: () => { Swal.close(); Swal.fire('Error', 'No se pudieron cargar las plantillas.', 'error'); },
    });
  }

  guardarDevolucion(): void {
    const monto = this.devolucionMonto();
    const motivo = this.devolucionMotivo().trim();
    if (!monto || monto <= 0) { this.toast.error('Error', 'Ingrese el monto a devolver'); return; }
    if (!motivo) { this.toast.error('Error', 'Ingrese el motivo de la devolución'); return; }

    this.guardandoDevolucion.set(true);
    this.service.crearDevolucion(this.id, {
      monto,
      motivo,
      documento_url: this.devolucionDocUrl() || null,
    }).subscribe({
      next: () => {
        this.toast.success('Devolución registrada', 'La solicitud fue guardada');
        this.guardandoDevolucion.set(false);
        this.showDevolucion.set(false);
        this.resetDevolucion();
        this.cargar();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo registrar la devolución');
        this.guardandoDevolucion.set(false);
      },
    });
  }

  cancelarDevolucion(id: number): void {
    Swal.fire({
      title: '¿Cancelar esta devolución?',
      text: 'La solicitud quedará marcada como cancelada.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#6b7280',
      cancelButtonColor: '#d1d5db',
      confirmButtonText: 'Sí, cancelar',
      cancelButtonText: 'No',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.cancelarDevolucion(id).subscribe({
          next: () => { this.toast.success('Cancelada', 'La devolución fue cancelada'); this.cargar(); },
          error: () => this.toast.error('Error', 'No se pudo cancelar'),
        });
      }
    });
  }

  aprobarDevolucion(id: number): void {
    Swal.fire({
      title: '¿Aprobar devolución?',
      text: 'Se marcará como aprobada y procederá el reembolso.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, aprobar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.resolverDevolucion(id, 'aprobada').subscribe({
          next: () => { this.toast.success('Aprobada', 'La devolución fue aprobada'); this.cargar(); },
          error: () => this.toast.error('Error', 'No se pudo aprobar'),
        });
      }
    });
  }

  rechazarDevolucion(id: number): void {
    Swal.fire({
      title: 'Rechazar devolución',
      input: 'textarea',
      inputLabel: 'Motivo del rechazo',
      inputPlaceholder: 'Explique por qué se rechaza esta solicitud...',
      inputAttributes: { rows: '3' },
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Rechazar',
      cancelButtonText: 'Cancelar',
      inputValidator: v => (!v?.trim() ? 'Ingrese el motivo del rechazo' : null),
    }).then(r => {
      if (r.isConfirmed) {
        this.service.resolverDevolucion(id, 'rechazada', r.value).subscribe({
          next: () => { this.toast.success('Rechazada', 'La devolución fue rechazada'); this.cargar(); },
          error: () => this.toast.error('Error', 'No se pudo rechazar'),
        });
      }
    });
  }



  estadoCompromisoLabel(estado: EstadoCompromisoCobro): string {
    return ESTADO_COMPROMISO_LABELS[estado] ?? estado;
  }

  estadoCompromisoClase(estado: EstadoCompromisoCobro): string {
    return ESTADO_COMPROMISO_CLASES[estado] ?? 'bg-default-100 text-default-500';
  }

  toggleCompromiso(): void {
    this.showCompromiso.update(v => !v);
    if (this.showCompromiso()) {
      this.compromisoFecha.set(this.hoyIsoAttr);
      this.compromisoHora.set('');
      this.compromisoMonto.set(null);
      this.compromisoObs.set('');
    }
  }

  guardarCompromiso(): void {
    if (!this.compromisoFecha()) { this.toast.error('Error', 'Seleccione la fecha comprometida'); return; }

    this.guardandoCompromiso.set(true);
    this.compromisoCobroService.create({
      id_ins: this.id,
      fecha_compromiso: this.compromisoFecha(),
      hora_compromiso: this.compromisoHora() || null,
      monto_comprometido: this.compromisoMonto(),
      observacion: this.compromisoObs().trim() || null,
    }).subscribe({
      next: () => {
        this.toast.success('Compromiso registrado', 'Se notificó al vendedor responsable');
        this.guardandoCompromiso.set(false);
        this.showCompromiso.set(false);
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar el compromiso'));
        this.guardandoCompromiso.set(false);
      },
    });
  }

  marcarCumplidoCompromiso(id: number): void {
    Swal.fire({
      title: '¿Marcar como cumplido?',
      text: 'Usa esta opción si el estudiante ya pagó y el pago se registrará más tarde.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, marcar cumplido',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.compromisoCobroService.cumplir(id).subscribe({
          next: () => {
            this.toast.success('Cumplido', 'El compromiso fue marcado como cumplido');
            this.historialCompromisoIdCargado = null;
            this.cargar();
          },
          error: (err) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el compromiso')),
        });
      }
    });
  }

  toggleHistorialCompromiso(id: number): void {
    this.showHistorialCompromiso.update(v => !v);
    if (this.showHistorialCompromiso() && this.historialCompromisoIdCargado !== id) {
      this.cargandoHistorialCompromiso.set(true);
      this.compromisoCobroService.getHistorial(id).subscribe({
        next: (logs) => {
          this.historialCompromiso.set(logs);
          this.historialCompromisoIdCargado = id;
          this.cargandoHistorialCompromiso.set(false);
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el historial'));
          this.cargandoHistorialCompromiso.set(false);
        },
      });
    }
  }

  motivoReprogramacionLabel(motivo: MotivoReprogramacion | null): string {
    if (!motivo) return '';
    return this.motivosReprogramacion.find(m => m.value === motivo)?.label ?? motivo;
  }

  toggleReprogramarCompromiso(id: number): void {
    this.reprogramarCompromisoId.set(id);
    this.reprogramarFecha.set('');
    this.reprogramarHora.set('');
    this.reprogramarMotivo.set('pidio_mas_tiempo');
    this.reprogramarObs.set('');
    this.showReprogramarCompromiso.set(true);
  }

  cerrarReprogramarCompromiso(): void {
    this.showReprogramarCompromiso.set(false);
    this.reprogramarCompromisoId.set(null);
  }

  guardarReprogramarCompromiso(): void {
    const id = this.reprogramarCompromisoId();
    if (!id) return;
    if (!this.reprogramarFecha()) { this.toast.error('Error', 'Seleccione la nueva fecha'); return; }

    this.guardandoReprogramar.set(true);
    this.compromisoCobroService.reprogramar(id, {
      nueva_fecha: this.reprogramarFecha(),
      nueva_hora: this.reprogramarHora() || null,
      motivo: this.reprogramarMotivo(),
      observacion: this.reprogramarObs().trim() || null,
    }).subscribe({
      next: () => {
        this.toast.success('Reprogramado', 'El compromiso fue reprogramado');
        this.guardandoReprogramar.set(false);
        this.cerrarReprogramarCompromiso();
        this.historialCompromisoIdCargado = null;
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo reprogramar el compromiso'));
        this.guardandoReprogramar.set(false);
      },
    });
  }



  descargandoPdf = signal(false);

  descargarPdf(): void {
    const ins = this.detalle()?.inscripcion;
    if (!ins) return;
    this.descargandoPdf.set(true);
    this.service.descargarPdf(ins.id_ins).subscribe({
      next: blob => {
        this.descargandoPdf.set(false);
        const url = URL.createObjectURL(blob);
        const ventana = window.open(url, '_blank');
        if (!ventana) {
          this.toast.warning('Bloqueado', 'El navegador bloqueó la ventana emergente. Habilita las ventanas emergentes para este sitio.');
        }
        setTimeout(() => URL.revokeObjectURL(url), 30000);
      },
      error: () => {
        this.descargandoPdf.set(false);
        this.toast.error('Error', 'No se pudo generar el comprobante PDF.');
      },
    });
  }

  showEnviarCorreo = signal(false);
  correoDestino = signal('');
  enviandoCorreo = signal(false);

  abrirEnviarCorreo(): void {
    const ins = this.detalle()?.inscripcion as any;
    this.correoDestino.set(ins?.est_email ?? ins?.estudiante_email ?? '');
    this.showEnviarCorreo.set(true);
  }

  cerrarEnviarCorreo(): void { this.showEnviarCorreo.set(false); }

  enviarPorCorreo(): void {
    const ins = this.detalle()?.inscripcion;
    const email = this.correoDestino().trim();
    if (!ins) return;
    if (!email) { this.toast.error('Error', 'Ingrese un correo electrónico.'); return; }

    this.enviandoCorreo.set(true);
    this.service.enviarComprobantePorCorreo(ins.id_ins, email).subscribe({
      next: res => {
        this.enviandoCorreo.set(false);
        this.showEnviarCorreo.set(false);
        this.toast.success('Enviado', `Comprobante enviado a ${res.email}.`);
      },
      error: (err) => {
        this.enviandoCorreo.set(false);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo enviar el comprobante por correo.'));
      },
    });
  }

  reglamento = signal<ReglamentoPrograma | null>(null);
  reglamentoLoading = signal(false);

  private idProgramaRef(): number | null {
    const ins = this.detalle()?.inscripcion as any;
    return ins?.id_programa ?? ins?.id_imp ?? null;
  }

  imprimir(): void {
    if (this.reglamento()) { window.print(); return; }
    const idRef = this.idProgramaRef();
    if (!idRef) { window.print(); return; }
    this.reglamentoLoading.set(true);
    this.service.getReglamento(idRef).subscribe({
      next: r => { this.reglamento.set(r); this.reglamentoLoading.set(false); window.print(); },
      error: () => { this.reglamentoLoading.set(false); window.print(); },
    });
  }



  showEditor = signal(false);
  guardando = signal(false);
  draft = signal<Partial<ReglamentoPrograma>>({});

  cargarReglamento(): void {
    if (this.reglamento()) return;
    const idRef = this.idProgramaRef();
    if (!idRef) return;
    this.reglamentoLoading.set(true);
    this.service.getReglamento(idRef).subscribe({
      next: r => { this.reglamento.set(r); this.reglamentoLoading.set(false); },
      error: () => this.reglamentoLoading.set(false),
    });
  }

  abrirEditor(): void {
    const r = this.reglamento();
    if (!r) return;
    this.draft.set({
      bienvenida: r.bienvenida ?? '',
      reglas_asistencia: r.reglas_asistencia ?? '',
      reglas_evaluacion: r.reglas_evaluacion ?? '',
      reglas_pagos: r.reglas_pagos ?? '',
      reglas_conducta: r.reglas_conducta ?? '',
      reglas_plataformas: r.reglas_plataformas ?? '',
      reglas_derechos: r.reglas_derechos ?? '',
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
    const idRef = this.idProgramaRef();
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

  estadoPagoImpresion(): 'pagado' | 'parcial' | 'pendiente' {
    const r = this.detalle()?.resumen;
    if (!r) return 'pendiente';
    if (r.pendiente != null && r.pendiente <= 0 && r.total_pagado > 0) return 'pagado';
    if (r.total_pagado > 0) return 'parcial';
    return 'pendiente';
  }

  colorEstadoImpresion(): string {
    const e = this.estadoPagoImpresion();
    if (e === 'pagado') return '#0F6E5C';
    if (e === 'parcial') return '#B4690E';
    return '#A83232';
  }

  badgeLabelImpresion(): string {
    return ({ pagado: 'Pagado', parcial: 'Pago Parcial', pendiente: 'Pendiente' } as Record<string, string>)[this.estadoPagoImpresion()];
  }
}
