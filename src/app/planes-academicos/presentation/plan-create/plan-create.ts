import { Component, inject, signal, computed } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators, FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PlanAcademicoService } from '../../application/services/plan-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

interface CuotaLocal {
  nro: number;
  descripcion: string;
  monto: number;
  fecha_vencimiento: string;
  dias_desde_inscripcion: number;
}

@Component({
  selector: 'app-plan-create',
  standalone: true,
  imports: [ReactiveFormsModule, FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './plan-create.html'
})
export class PlanCreate {
  private service  = inject(PlanAcademicoService);
  private toast    = inject(ToastService);
  private router   = inject(Router);
  private fb       = inject(FormBuilder);

  submitting = signal(false);
  qrPreview = signal<string | null>(null);
  private qrFile: File | null = null;

  // Cuotas en memoria (antes de guardar)
  cuotas = signal<CuotaLocal[]>([]);
  modoCuotas = signal<'fecha' | 'dias'>('fecha'); // fecha=fijas, dias=relativo

  form = this.fb.group({
    titulo:     ['', [Validators.required, Validators.maxLength(200)]],
    costo:      ['', [Validators.required, Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
    nro_cuotas: ['1', [Validators.required, Validators.pattern(/^[1-9]\d*$/)]],
    descuento:  ['0', [Validators.pattern(/^(100(\.0{1,2})?|[0-9]{1,2}(\.\d{1,2})?)$/)]],
    estado:     [1],
  });

  nroCuotasNum = signal<number>(1);

  costoNum = signal<number>(0);

  totalCuotas = computed(() => {
    return this.cuotas().reduce((a, c) => a + (c.monto || 0), 0);
  });

  costoPorCuota = computed(() => {
    const n = this.nroCuotasNum();
    const c = this.costoNum();
    if (n > 0 && c > 0) return 'Bs. ' + (c / n).toFixed(2);
    return '';
  });

  constructor() {
    this.form.get('nro_cuotas')!.valueChanges.subscribe(v => {
      this.nroCuotasNum.set(parseInt(v ?? '1', 10) || 1);
      this.recalcularCuotas();
    });
    this.form.get('costo')!.valueChanges.subscribe(v => {
      this.costoNum.set(parseFloat(v ?? '0') || 0);
      this.recalcularMontos();
    });
  }

  private recalcularCuotas() {
    const n = this.nroCuotasNum();
    const c = this.costoNum();
    if (n <= 1) { this.cuotas.set([]); return; }

    const nombres = ['Matricula', '1ra Cuota', '2da Cuota', '3ra Cuota', '4ta Cuota', '5ta Cuota'];
    const monto = c > 0 ? parseFloat((c / n).toFixed(2)) : 0;
    const hoy = new Date();

    const nuevas: CuotaLocal[] = [];
    for (let i = 0; i < n; i++) {
      const existente = this.cuotas()[i];
      const fecha = new Date(hoy);
      fecha.setMonth(fecha.getMonth() + i);
      nuevas.push({
        nro: i + 1,
        descripcion: existente?.descripcion ?? (nombres[i] ?? ('Cuota ' + (i + 1))),
        monto: monto,
        fecha_vencimiento: existente?.fecha_vencimiento ?? fecha.toISOString().split('T')[0],
        dias_desde_inscripcion: existente?.dias_desde_inscripcion ?? (i * 30),
      });
    }
    this.cuotas.set(nuevas);
  }

  private recalcularMontos() {
    const c = this.costoNum();
    const n = this.nroCuotasNum();
    if (c <= 0 || n <= 1) return;
    const monto = parseFloat((c / n).toFixed(2));
    this.cuotas.set(this.cuotas().map(q => ({ ...q, monto })));
  }

  
  forzarActualizacion() {
    this.cuotas.set([...this.cuotas()]);
  }

  onMontoChange(index: number) {
    const total = this.costoNum();
    const cuotas = [...this.cuotas()];
    const n = cuotas.length;

    // Solo auto-balancear si no es la ultima cuota
    if (index < n - 1 && total > 0) {
      let sumaFija = 0;
      for (let i = 0; i <= index; i++) {
        sumaFija += (cuotas[i].monto || 0);
      }

      let resto = total - sumaFija;
      if (resto < 0) resto = 0;

      const quedan = n - 1 - index;
      const montoRepartido = parseFloat((resto / quedan).toFixed(2));

      for (let i = index + 1; i < n; i++) {
        cuotas[i].monto = montoRepartido;
      }

      // Ajustar centavos en la ultima cuota para cuadre perfecto
      let sumaNueva = 0;
      for (let i = 0; i < n - 1; i++) {
        sumaNueva += (cuotas[i].monto || 0);
      }
      
      let ultima = total - sumaNueva;
      cuotas[n - 1].monto = parseFloat((ultima > 0 ? ultima : 0).toFixed(2));
    }

    this.cuotas.set(cuotas);
  }


  distribuirAutomaticamente() {
    this.recalcularCuotas();
  }

  onQrChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.qrFile = file;
    const reader = new FileReader();
    reader.onload = (e) => this.qrPreview.set(e.target?.result as string);
    reader.readAsDataURL(file);
  }

  quitarQr(): void { this.qrFile = null; this.qrPreview.set(null); }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);

    const formVals = this.form.value;
    const fd = new FormData();
    fd.append('titulo', formVals.titulo ?? '');
    fd.append('costo', String(formVals.costo ?? '0'));
    fd.append('nro_cuotas', String(formVals.nro_cuotas ?? '1'));
    fd.append('descuento', String(formVals.descuento ?? '0'));
    fd.append('estado', String(formVals.estado ?? 1));
    fd.append('modo_fechas', this.modoCuotas() === 'fecha' ? 'fijo' : 'relativo');
    fd.append('id_plan', String(Math.floor(Date.now() / 1000)));
    if (this.qrFile) fd.append('qr_image', this.qrFile);

    this.service.create(fd as any).subscribe({
      next: (plan: any) => {
        // Si hay cuotas configuradas, crearlas
        const cuotas = this.cuotas();
        if (cuotas.length > 0 && plan?.id_plan) {
          const calls = cuotas.map(q =>
            this.service.createCuota({
              id_plan: plan.id_plan,
              nro_pago: String(q.nro),
              tipo_tramite: q.descripcion,
              monto_a_pagar: q.monto,
              fecha_fin: this.modoCuotas() === 'fecha' ? q.fecha_vencimiento : null,
              dias_desde_inscripcion: this.modoCuotas() === 'dias' ? q.dias_desde_inscripcion : null,
              obligatorio: 1,
            })
          );
          // Ejecutar en secuencia
          const runNext = (index: number) => {
            if (index >= calls.length) {
              this.toast.success('Plan creado', 'Plan y cuotas guardados correctamente');
              this.router.navigate(['/cenefco/planes-academicos']);
              return;
            }
            calls[index].subscribe({ next: () => runNext(index + 1), error: () => runNext(index + 1) });
          };
          runNext(0);
        } else {
          this.toast.success('Plan creado', 'Plan de pago registrado');
          this.router.navigate(['/cenefco/planes-academicos']);
        }
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      }
    });
  }
}
