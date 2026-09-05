import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CategoriaNoticiaService } from '../../application/services/categoria-noticia.service';
import { CategoriaNoticia } from '../../domain/models/categoria-noticia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-categoria-noticia-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './categoria-noticia-create.html',
  styles: ``
})
export class CategoriaNoticiaCreate {
  private fb                     = inject(FormBuilder);
  private categoriaNoticiaService = inject(CategoriaNoticiaService);
  private toast                  = inject(ToastService);
  private router                 = inject(Router);

  submitting = signal(false);
  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.categoriaNoticiaService.create(this.form.value as unknown as Partial<CategoriaNoticia>).subscribe({
      next: () => { this.toast.success('¡Creada!', 'La categoría ha sido creada correctamente'); this.router.navigate(['/cenefco/categorias-noticia']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la categoría')); this.submitting.set(false); }
    });
  }
}
