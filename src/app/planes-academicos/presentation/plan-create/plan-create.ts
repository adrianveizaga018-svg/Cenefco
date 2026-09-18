import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PlanAcademicoService } from '../../application/services/plan-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-plan-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './plan-create.html' })
export class PlanCreate {
  private service  = inject(PlanAcademicoService);
  private toast    = inject(ToastService);
  private router   = inject(Router);
  private fb       = inject(FormBuilder);

  submitting = signal(false);
  costoPorCuota = signal('');
  qrPreview = signal<string | null>(null);
  private qrFile: File | null = null;

  form = this.fb.group({
    titulo:      ['', [Validators.required, Validators.maxLength(200)]],
    costo:       ['', [Validators.required, Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
    nro_cuotas:  ['1', [Validators.required, Validators.pattern(/^[1-9]\d*$/)]],
    descuento:   ['0', [Validators.pattern(/^(100(\.0{1,2})?|[0-9]{1,2}(\.\d{1,2})?)$/)]],
    estado:      [1],
  });

  constructor() {
    const calcCuota = () => {
      const costo  = parseFloat(this.form.get('costo')!.value ?? '');
      const cuotas = parseInt(this.form.get('nro_cuotas')!.value ?? '1', 10);
      if (costo > 0 && cuotas > 0) {
        this.costoPorCuota.set('Bs. ' + (costo / cuotas).toFixed(2));
      } else {
        this.costoPorCuota.set('');
      }
    };
    this.form.get('costo')!.valueChanges.subscribe(calcCuota);
    this.form.get('nro_cuotas')!.valueChanges.subscribe(calcCuota);
  }

  onQrChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.qrFile = file;
    const reader = new FileReader();
    reader.onload = (e) => this.qrPreview.set(e.target?.result as string);
    reader.readAsDataURL(file);
  }

  quitarQr(): void {
    this.qrFile = null;
    this.qrPreview.set(null);
  }

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
    fd.append('id_plan', String(Math.floor(Date.now() / 1000)));
    if (this.qrFile) fd.append('qr_image', this.qrFile);

    this.service.create(fd as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Plan de pago registrado'); this.router.navigate(['/cenefco/planes-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}


