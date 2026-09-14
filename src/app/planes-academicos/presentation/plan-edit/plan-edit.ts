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
        this.loading.set(false);
        this.actualizarCostoCuota();
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar el plan'); this.router.navigate(['/cenefco/planes-academicos']); }
    });

    this.form.get('costo')!.valueChanges.subscribe(() => this.actualizarCostoCuota());
    this.form.get('nro_cuotas')!.valueChanges.subscribe(() => this.actualizarCostoCuota());
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
    
    // Convertir a string para que Laravel no rechace la validación
    const formVals = this.form.value;
    const payload = {
      ...formVals,
      nro_cuotas: String(formVals.nro_cuotas || '1'),
      costo: String(formVals.costo || '0'),
      descuento: String(formVals.descuento || '0')
    };

    this.service.update(this.id, payload as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Plan actualizado correctamente'); this.router.navigate(['/cenefco/planes-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}

