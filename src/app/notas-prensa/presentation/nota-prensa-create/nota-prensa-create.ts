import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { NotaPrensaService } from '../../application/services/nota-prensa.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-nota-prensa-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './nota-prensa-create.html' })
export class NotaPrensaCreate {
  private service = inject(NotaPrensaService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);

  form = this.fb.group({
    titulo:            ['', [Validators.required, Validators.maxLength(300)]],
    medio:             ['', [Validators.required, Validators.maxLength(200)]],
    logo_medio_url:    [''],
    logo_medio_alt:    [''],
    resumen:           [''],
    url_noticia:       [''],
    fecha_publicacion: ['', [Validators.required]],
    destacada:         [false],
    orden:             [0],
    activo:            [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creada!', 'Nota de prensa registrada'); this.router.navigate(['/cenefco/notas-prensa']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
