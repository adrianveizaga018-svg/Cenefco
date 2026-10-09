import { Component, OnInit, computed, inject, input, output, signal } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { extractErrorMessage } from '../../../utils/http-error';
import { BancoCuenta, EstudianteService, InfoPago, PlanCatalogo, ProgramaCatalogo } from '../../application/services/estudiante.service';

type Metodo = 'qr' | 'transferencia' | 'deposito';

/**
 * Diálogo para pagar con comprobante desde el panel del estudiante.
 * - Con `idIns`: paga una cuota de una inscripción que ya tiene.
 * - Con `programa`: se inscribe a un programa de pago con el primer pago.
 * - Preinscripción del portal (sin plan): primero elige el plan; si es gratuito queda inscrito.
 * El comprobante queda en revisión hasta que Caja lo apruebe.
 */
@Component({
  selector: 'app-comprobante-pago',
  standalone: true,
  imports: [FormsModule, NgIcon, DecimalPipe, DatePipe],
  templateUrl: './comprobante-pago.html',
})
export class ComprobantePagoComponent implements OnInit {
  private service = inject(EstudianteService);

  readonly idIns         = input<number | null>(null);
  readonly programa      = input<ProgramaCatalogo | null>(null);
  readonly montoSugerido = input<number | null>(null);
  readonly cerrar  = output<void>();
  readonly enviado = output<string>();

  cargando = signal(true);
  enviando = signal(false);
  error    = signal('');
  info     = signal<InfoPago | null>(null);
  bancos   = signal<BancoCuenta[]>([]);
  planId   = signal<number | null>(null);

  monto         = signal<number | null>(null);
  fecha         = signal(new Date().toISOString().slice(0, 10));
  metodo        = signal<Metodo>('qr');
  bancoId       = signal<number | null>(null);
  nroBoleta     = signal('');
  archivo       = signal<File | null>(null);
  readonly hoy  = new Date().toISOString().slice(0, 10);

  readonly esInscripcion = computed(() => !!this.programa());
  /** Hay que elegir plan: inscripción nueva o preinscripción del portal sin plan. */
  readonly eligePlan = computed(() => this.esInscripcion() || !!this.info()?.requiere_plan);
  readonly planesPago = computed(() => this.esInscripcion()
    ? (this.programa()?.planes ?? []).filter(p => !p.gratuito)
    : (this.info()?.requiere_plan ? this.info()!.planes : []));
  readonly planSel = computed<PlanCatalogo | null>(() => this.planesPago().find(p => p.id_plan === this.planId()) ?? null);
  /** Plan gratuito elegido: no hay nada que pagar, solo confirmar. */
  readonly planGratis = computed(() => !!this.planSel()?.gratuito);
  readonly titulo = computed(() => this.programa()?.nombre ?? this.info()?.programa ?? 'Pago');
  readonly qrUrl = computed(() => this.eligePlan() ? this.planSel()?.qr_url ?? null : this.info()?.qr_url ?? null);
  readonly maximo = computed(() => this.eligePlan() ? (this.planSel()?.costo ?? null) : (this.info()?.saldo ?? null));

  ngOnInit(): void {
    if (this.esInscripcion()) {
      const primero = this.planesPago()[0];
      if (primero) this.elegirPlan(primero);
      this.service.getBancos().subscribe({
        next: b => { this.bancos.set(b); this.cargando.set(false); },
        error: () => this.cargando.set(false),
      });
      return;
    }

    const id = this.idIns();
    if (!id) { this.cerrar.emit(); return; }
    this.service.getInfoPago(id).subscribe({
      next: (i) => {
        this.info.set(i);
        this.bancos.set(i.bancos);
        this.monto.set(this.montoSugerido() ?? i.proxima?.saldo ?? i.saldo);
        if (i.requiere_plan && i.planes.length) this.elegirPlan(i.planes[0]);
        this.cargando.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.error.set(extractErrorMessage(err, 'No se pudo cargar la información de pago.'));
        this.cargando.set(false);
      },
    });
  }

  elegirPlan(p: PlanCatalogo): void {
    this.planId.set(p.id_plan);
    this.monto.set(p.primer_pago || p.costo);
  }

  onArchivo(ev: Event): void {
    const f = (ev.target as HTMLInputElement).files?.[0] ?? null;
    if (f && f.size > 5 * 1024 * 1024) {
      this.error.set('El archivo pesa más de 5 MB. Sube una foto más liviana o un PDF.');
      this.archivo.set(null);
      return;
    }
    this.error.set('');
    this.archivo.set(f);
  }

  enviar(): void {
    if (this.planGratis()) { this.confirmarGratis(); return; }
    const monto = Number(this.monto());
    if (!monto || monto <= 0) { this.error.set('Escribe el monto que pagaste.'); return; }
    const max = this.maximo();
    if (max !== null && monto > max + 0.01) { this.error.set(`El monto no puede ser mayor a Bs. ${max.toFixed(2)}.`); return; }
    if (!this.archivo()) { this.error.set('Sube la foto o el PDF de tu comprobante.'); return; }
    if (this.eligePlan() && !this.planSel()) { this.error.set('Elige un plan de pago.'); return; }

    const fd = new FormData();
    fd.append('monto', String(monto));
    fd.append('fecha_deposito', this.fecha());
    fd.append('metodo_pago', this.metodo());
    if (this.metodo() !== 'qr' && this.bancoId()) fd.append('tipo_banco_id', String(this.bancoId()));
    if (this.nroBoleta().trim()) fd.append('nro_boleta', this.nroBoleta().trim());
    fd.append('comprobante', this.archivo()!);

    let peticion;
    if (this.esInscripcion()) {
      fd.append('id_imp', String(this.programa()!.id_imp));
      fd.append('id_plan', String(this.planSel()!.id_plan));
      peticion = this.service.inscribirseConComprobante(fd);
    } else {
      fd.append('id_ins', String(this.idIns()));
      if (this.info()?.requiere_plan) fd.append('id_plan', String(this.planSel()!.id_plan));
      peticion = this.service.enviarComprobante(fd);
    }

    this.enviando.set(true);
    this.error.set('');
    peticion.subscribe({
      next: (res) => { this.enviando.set(false); this.enviado.emit(res.message); },
      error: (err: HttpErrorResponse) => {
        this.enviando.set(false);
        this.error.set(extractErrorMessage(err, 'No se pudo enviar el comprobante.'));
      },
    });
  }

  private confirmarGratis(): void {
    this.enviando.set(true);
    this.error.set('');
    this.service.asignarPlan(this.idIns()!, this.planSel()!.id_plan).subscribe({
      next: (res) => { this.enviando.set(false); this.enviado.emit(res.message); },
      error: (err: HttpErrorResponse) => {
        this.enviando.set(false);
        this.error.set(extractErrorMessage(err, 'No se pudo confirmar la inscripción.'));
      },
    });
  }
}
