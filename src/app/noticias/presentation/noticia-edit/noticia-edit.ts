import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { NoticiaService } from '../../application/services/noticia.service';
import { Noticia } from '../../domain/models/noticia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-noticia-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './noticia-edit.html',
  styles: ``
})
export class NoticiaEdit implements OnInit {
  private fb             = inject(FormBuilder);
  private noticiaService = inject(NoticiaService);
  private toast          = inject(ToastService);
  private router         = inject(Router);
  private route          = inject(ActivatedRoute);

  submitting     = signal(false);
  loadingNoticia = signal(true);
  private id!: number;
  private slug!: string;

  form = this.fb.group({
    titulo:    ['', [Validators.required, Validators.maxLength(255)]],
    estado:    ['borrador', [Validators.required]],
    destacada: [false],
  });

  ngOnInit(): void {
    this.slug = this.route.snapshot.paramMap.get('slug') ?? '';
    this.noticiaService.getBySlug(this.slug).subscribe({
      next: (noticia) => { this.id = noticia.id; this.form.patchValue({ titulo: noticia.titulo, estado: noticia.estado, destacada: noticia.destacada }); this.loadingNoticia.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la noticia')); this.router.navigate(['/cenefco/noticias']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.noticiaService.update(this.id, this.form.value as unknown as Partial<Noticia>).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'La noticia ha sido actualizada correctamente'); this.router.navigate(['/cenefco/noticias']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la noticia')); this.submitting.set(false); }
    });
  }
}
