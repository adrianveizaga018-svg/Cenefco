import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EtiquetaService } from '../../application/services/etiqueta.service';
import { Etiqueta } from '../../domain/models/etiqueta.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-etiqueta-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './etiqueta-create.html',
  styles: ``
})
export class EtiquetaCreate {
  private fb             = inject(FormBuilder);
  private etiquetaService = inject(EtiquetaService);
  private toast          = inject(ToastService);
  private router         = inject(Router);

  submitting = signal(false);

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(100)]],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.etiquetaService.create(this.form.value as unknown as Partial<Etiqueta>).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'La etiqueta ha sido creada correctamente');
        this.router.navigate(['/cenefco/etiquetas']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la etiqueta'));
        this.submitting.set(false);
      }
    });
  }
}
