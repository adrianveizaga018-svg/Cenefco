import { Component, inject, signal, computed, output, input } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FechaPagoService } from '../../application/services/fecha-pago.service';
import { ToastService } from '../../../common/application/services/toast.service';

type Intervalo = 'mensual' | 'quincenal' | 'semanal';

interface CuotaPreview {
  nro:          number;
  fecha_inicio: string;
  fecha_fin:    string;
  monto:        number;
}

const INTERVALOS: { value: Intervalo; label: string; dias: number }[] = [
  { value: 'mensual',   label: 'Mensual (30 días)',   dias: 30 },
  { value: 'quincenal', label: 'Quincenal (15 días)', dias: 15 },
  { value: 'semanal',   label: 'Semanal (7 días)',    dias: 7 },
];

function addDias(fecha: string, dias: number): string {
  const d = new Date(fecha + 'T00:00:00');
  d.setDate(d.getDate() + dias);
  return d.toISOString().split('T')[0];
}

@Component({
  selector: 'app-asistente-cuotas',
  standalone: true,
  imports: [FormsModule, NgIcon, DecimalPipe],
  templateUrl: './asistente-cuotas.html',
})
export class AsistenteCuotas {
  private service = inject(FechaPagoService);
  private toast   = inject(ToastService);

  idPlan      = input.required<number>();
  generado    = output<void>();

  nroCuotas    = signal(1);
  montoTotal   = signal<number | null>(null);
  fechaInicio  = signal(new Date().toISOString().split('T')[0]);
  intervalo    = signal<Intervalo>('mensual');
  tipoTramite  = signal('Cuota');
  guardando    = signal(false);

  readonly intervalos = INTERVALOS;

  preview = computed<CuotaPreview[]>(() => {
    const n     = this.nroCuotas();
    const total = this.montoTotal();
    const fecha = this.fechaInicio();
    const int   = INTERVALOS.find(i => i.value === this.intervalo())!;

    if (!total || n < 1 || !fecha) return [];

    const montoCuota  = Math.round((total / n) * 100) / 100;
    const cuotas: CuotaPreview[] = [];

    let inicio = fecha;
    for (let i = 1; i <= Math.min(n, 12); i++) {
      const fin = addDias(inicio, int.dias - 1);
      cuotas.push({ nro: i, fecha_inicio: inicio, fecha_fin: fin, monto: montoCuota });
      inicio = addDias(inicio, int.dias);
    }
    return cuotas;
  });

  totalPreview = computed(() =>
    this.preview().reduce((s, c) => s + c.monto, 0)
  );

  guardar(): void {
    const total = this.montoTotal();
    if (!total || total <= 0) { this.toast.error('Error', 'Ingrese el monto total'); return; }
    if (this.nroCuotas() < 1 || this.nroCuotas() > 60) { this.toast.error('Error', 'Entre 1 y 60 cuotas'); return; }
    if (!this.fechaInicio()) { this.toast.error('Error', 'Seleccione la fecha de inicio'); return; }

    this.guardando.set(true);
    this.service.generarLote({
      id_plan:      this.idPlan(),
      nro_cuotas:   this.nroCuotas(),
      monto_total:  total,
      fecha_inicio: this.fechaInicio(),
      intervalo:    this.intervalo(),
      tipo_tramite: this.tipoTramite() || 'Cuota',
    }).subscribe({
      next: res => {
        this.toast.success(
          'Lote generado',
          `Se crearon ${res.cuotas_generadas} cuota(s) correctamente`
        );
        this.guardando.set(false);
        this.generado.emit();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar las cuotas');
        this.guardando.set(false);
      },
    });
  }
}
