import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SecretariaService } from '../../application/services/secretaria.service';
import { Secretaria } from '../../domain/models/secretaria.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-secretaria-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './secretaria-create.html',
  styles: ``
})
export class SecretariaCreate {
  private fb                = inject(FormBuilder);
  private secretariaService = inject(SecretariaService);
  private toast             = inject(ToastService);
  private router            = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(150)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.secretariaService.create(this.form.value as unknown as Partial<Secretaria>).subscribe({
      next: () => { this.toast.success('¡Creada!', 'La secretaría ha sido creada correctamente'); this.router.navigate(['/cenefco/secretarias']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la secretaría')); this.submitting.set(false); }
    });
  }
}
