import { Component, inject, signal, computed } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators, FormsModule } from '@angular/forms';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PlanAcademicoService } from '../../application/services/plan-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';
import { forkJoin, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

interface CuotaLocal {
  id_fechapago?: number;
  nro: number;
  descripcion: string;
  monto: number;
  fecha_vencimiento: string;
  dias_desde_inscripcion: number;
}

@Component({
  selector: 'app-plan-edit',
  standalone: true,
  imports: [ReactiveFormsModule, FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './plan-edit.html'
})
export class PlanEdit {
  private route = inject(ActivatedRoute);
  id = Number(this.route.snapshot.paramMap.get('id'));
  loading = signal(true);
  private cuotasOriginales: number[] = [];
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

  ngOnInit() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue({
          titulo: d.titulo,
          costo: d.costo,
          nro_cuotas: String(d.nro_cuotas || 1),
          estado: d.estado
        });
        if (d.qr_image_url) this.qrPreview.set(d.qr_image_url);
        this.modoCuotas.set(d.modo_fechas === 'relativo' ? 'dias' : 'fecha');
        this.nroCuotasNum.set(Number(d.nro_cuotas || 1));
        this.costoNum.set(Number(d.costo || 0));

        this.service.getCuotas(this.id).subscribe({
          next: (res) => {
            const arr = res.data ?? res;
            const locales = arr.map((q: any) => {
              this.cuotasOriginales.push(q.id_fechapago);
              return {
                id_fechapago: q.id_fechapago,
                nro: Number(q.nro_pago),
                descripcion: q.tipo_tramite,
                monto: Number(q.monto_a_pagar),
                fecha_vencimiento: q.fecha_fin || '',
                dias_desde_inscripcion: Number(q.dias_desde_inscripcion || 0)
              };
            });
            this.cuotas.set(locales);
            this.loading.set(false);
          },
          error: () => this.loading.set(false)
        });
      },
      error: () => { 
        this.toast.error('Error', 'No se pudo cargar el plan'); 
        this.router.navigate(['/cenefco/planes-academicos']); 
      }
    });
  }

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
        id_fechapago: existente?.id_fechapago,
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
    fd.append('_method', 'PUT');
    fd.append('titulo', formVals.titulo ?? '');
    fd.append('costo', String(formVals.costo ?? '0'));
    fd.append('nro_cuotas', String(formVals.nro_cuotas ?? '1'));
    fd.append('estado', String(formVals.estado ?? 1));
    fd.append('modo_fechas', this.modoCuotas() === 'fecha' ? 'fijo' : 'relativo');
    if (this.qrFile) fd.append('qr_image', this.qrFile);
    else if (this.qrPreview() === null) fd.append('remove_qr', '1');

    this.service.updateWithFormData(this.id, fd).subscribe({
      next: () => {
        const cuotasF = this.cuotas();
        const idsMantener = cuotasF.map(q => q.id_fechapago).filter(id => id);
        const paraBorrar = this.cuotasOriginales.filter(id => !idsMantener.includes(id));
        const calls = [];
        
        for (const b of paraBorrar) calls.push(this.service.deleteCuota(b).pipe(catchError(() => of(null))));

        for (const q of cuotasF) {
          const body = {
            id_plan: this.id,
            nro_pago: String(q.nro),
            tipo_tramite: q.descripcion,
            monto_a_pagar: q.monto,
            fecha_fin: this.modoCuotas() === 'fecha' ? q.fecha_vencimiento : null,
            dias_desde_inscripcion: this.modoCuotas() === 'dias' ? q.dias_desde_inscripcion : null,
            obligatorio: 1,
          };
          if (q.id_fechapago) calls.push(this.service.updateCuota(q.id_fechapago, body).pipe(catchError(() => of(null))));
          else calls.push(this.service.createCuota(body).pipe(catchError(() => of(null))));
        }

        if (calls.length > 0) {
          forkJoin(calls).subscribe(() => {
            this.toast.success('¡Actualizado!', 'Plan y cuotas guardados');
            this.router.navigate(['/cenefco/planes-academicos']);
          });
        } else {
          this.toast.success('¡Actualizado!', 'Plan actualizado correctamente');
          this.router.navigate(['/cenefco/planes-academicos']);
        }
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
        this.submitting.set(false);
      }
    });
  }
}
