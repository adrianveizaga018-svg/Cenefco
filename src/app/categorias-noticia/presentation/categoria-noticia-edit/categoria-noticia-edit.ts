import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CategoriaNoticiaService } from '../../application/services/categoria-noticia.service';
import { CategoriaNoticia } from '../../domain/models/categoria-noticia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-categoria-noticia-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './categoria-noticia-edit.html',
  styles: ``
})
export class CategoriaNoticiaEdit implements OnInit {
  private fb                      = inject(FormBuilder);
  private categoriaNoticiaService  = inject(CategoriaNoticiaService);
  private toast                   = inject(ToastService);
  private router                  = inject(Router);
  private route                   = inject(ActivatedRoute);

  submitting           = signal(false);
  loadingCategoriaNoticia = signal(true);
  private id!: number;

  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.categoriaNoticiaService.getById(this.id).subscribe({
      next: (categoria) => { this.form.patchValue({ nombre: categoria.nombre }); this.loadingCategoriaNoticia.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la categoría')); this.router.navigate(['/cenefco/categorias-noticia']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.categoriaNoticiaService.update(this.id, this.form.value as unknown as Partial<CategoriaNoticia>).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'La categoría ha sido actualizada correctamente'); this.router.navigate(['/cenefco/categorias-noticia']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la categoría')); this.submitting.set(false); }
    });
  }
}
