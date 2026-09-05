import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AliadoService } from '../../application/services/aliado.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'app-aliado-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './aliado-create.html',
})
export class AliadoCreate {
  private service = inject(AliadoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);

  submitting = signal(false);

  form = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(200)]],
    logo_url:    ['', [Validators.required, Validators.maxLength(255)]],
    logo_alt:    ['', [Validators.maxLength(255)]],
    url_sitio:   ['', [Validators.maxLength(255)]],
    descripcion: ['', [Validators.maxLength(500)]],
    tipo:        ['', [Validators.maxLength(100)]],
    orden:       [0],
    activo:      [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Aliado registrado exitosamente');
        this.router.navigate(['/cenefco/aliados']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar el aliado'));
        this.submitting.set(false);
      }
    });
  }
}
