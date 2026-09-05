import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { TipoPagoService } from '../../application/services/tipo-pago.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-pago-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './tipo-pago-create.html',
})
export class TipoPagoCreate {
  private service = inject(TipoPagoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_tipopago: [this.autoId, [Validators.required]],
    titulo:      ['', [Validators.required, Validators.maxLength(200)]],
    grupo_pago:  [''],
    estado:      [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Tipo de pago registrado'); this.router.navigate(['/cenefco/tipos-pago']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
