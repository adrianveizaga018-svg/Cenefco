import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ExpedidoService } from '../../application/services/expedido.service';
import { Expedido } from '../../domain/models/expedido.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-expedido-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './expedido-create.html',
  styles: ``
})
export class ExpedidoCreate {
  private fb      = inject(FormBuilder);
  private service = inject(ExpedidoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  submitting = signal(false);

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(100)]],
    orden:  [0],
    activo: [true],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.create(this.form.value as Partial<Expedido>).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Registro creado correctamente');
        this.router.navigate(['/cenefco/expedido']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el registro'));
        this.submitting.set(false);
      }
    });
  }
}
