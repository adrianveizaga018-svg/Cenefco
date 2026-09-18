import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PlanAcademicoService } from '../../application/services/plan-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'app-plan-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './plan-edit.html',
})
export class PlanEdit {
  private service = inject(PlanAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);

  submitting    = signal(false);
  loading       = signal(true);
  costoPorCuota = signal('');
  id = Number(this.route.snapshot.paramMap.get('id'));
  qrPreview = signal<string | null>(null);
  private qrFile: File | null = null;

  form = this.fb.group({
    titulo:     ['', [Validators.required, Validators.maxLength(200)]],
    costo:      ['', [Validators.required, Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
    nro_cuotas: ['1', [Validators.required, Validators.pattern(/^[1-9]\d*$/)]],
    descuento:  ['0', [Validators.pattern(/^(100(\.0{1,2})?|[0-9]{1,2}(\.\d{1,2})?)$/)]],
    estado:     [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue(d as any);
        if (d.qr_image_url) this.qrPreview.set(d.qr_image_url);
        this.loading.set(false);
        this.actualizarCostoCuota();
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar el plan'); this.router.navigate(['/cenefco/planes-academicos']); }
    });

    this.form.get('costo')!.valueChanges.subscribe(() => this.actualizarCostoCuota());
    this.form.get('nro_cuotas')!.valueChanges.subscribe(() => this.actualizarCostoCuota());
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

  actualizarCostoCuota() {
    const costo  = parseFloat(this.form.get('costo')!.value ?? '');
    const cuotas = parseInt(this.form.get('nro_cuotas')!.value ?? '1', 10);
    if (costo > 0 && cuotas > 0) {
      this.costoPorCuota.set('Bs. ' + (costo / cuotas).toFixed(2));
    } else {
      this.costoPorCuota.set('');
    }
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    
    const formVals = this.form.value;
    const fd = new FormData();
    // Use _method for PUT simulating POST in FormData
    fd.append('_method', 'PUT');
    fd.append('titulo', formVals.titulo ?? '');
    fd.append('costo', String(formVals.costo ?? '0'));
    fd.append('nro_cuotas', String(formVals.nro_cuotas ?? '1'));
    fd.append('descuento', String(formVals.descuento ?? '0'));
    fd.append('estado', String(formVals.estado ?? 1));
    if (this.qrFile) fd.append('qr_image', this.qrFile);
    else if (this.qrPreview() === null) fd.append('remove_qr', '1'); // Optional, depending on backend

    // Note: using POST with _method=PUT is standard Laravel for file uploads.
    this.service.updateWithFormData(this.id, fd).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Plan actualizado correctamente'); this.router.navigate(['/cenefco/planes-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}

