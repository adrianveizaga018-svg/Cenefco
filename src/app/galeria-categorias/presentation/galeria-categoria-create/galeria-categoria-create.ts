import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { GaleriaCategoriaService } from '../../application/services/galeria-categoria.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-galeria-categoria-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './galeria-categoria-create.html' })
export class GaleriaCategoriaCreate {
  private service = inject(GaleriaCategoriaService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);

  form = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(200)]],
    slug:        ['', [Validators.maxLength(200)]],
    descripcion: [''],
    orden:       [0],
    activo:      [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creada!', 'Categoría registrada'); this.router.navigate(['/cenefco/galeria-categorias']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
