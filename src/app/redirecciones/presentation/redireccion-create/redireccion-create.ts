import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { RedireccionService } from '../../application/services/redireccion.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-redireccion-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './redireccion-create.html' })
export class RedireccionCreate {
  private service = inject(RedireccionService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);

  form = this.fb.group({
    url_origen:   ['', [Validators.required]],
    url_destino:  ['', [Validators.required]],
    codigo_http:  [301],
    activo:       [true],
    notas:        [''],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creada!', 'Redirección registrada'); this.router.navigate(['/cenefco/redirecciones']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
