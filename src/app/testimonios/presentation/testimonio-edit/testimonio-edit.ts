import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { TestimonioService } from '../../application/services/testimonio.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-testimonio-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './testimonio-edit.html' })
export class TestimonioEdit {
  private service    = inject(TestimonioService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private route      = inject(ActivatedRoute);
  private fb         = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);

  submitting    = signal(false);
  loading       = signal(true);
  uploadingFoto = signal(false);
  fotoPreview   = signal<string | null>(null);
  private id!: number;
  private slug = this.route.snapshot.paramMap.get('slug') ?? '';

  form = this.fb.group({
    nombre:       ['', [Validators.required, Validators.maxLength(200)]],
    cargo:        [''],
    empresa:      [''],
    testimonio:   ['', [Validators.required]],
    calificacion: [5, [Validators.min(1), Validators.max(5)]],
    foto_url:     [''],
    foto_alt:     [''],
    programa_id:  [null as number | null],
    destacado:    [false],
    orden:        [0],
    estado:       ['publicado'],
  });

  constructor() {
    this.service.getBySlug(this.slug).subscribe({
      next: (data) => {
        this.id = data.id;
        this.form.patchValue(data as any);
        if (data.foto_url) {
          const url = data.foto_url;
          this.fotoPreview.set(
            url.startsWith('http') || url.startsWith('/storage') ? url : `/storage/${url}`
          );
        }
        this.loading.set(false);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar el testimonio'); this.router.navigate(['/cenefco/testimonios']); }
    });
  }

  onFotoSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.fotoPreview,
      uploading:   this.uploadingFoto,
      onSuccess:   (url) => this.form.patchValue({ foto_url: url }),
      fallbackMsg: 'No se pudo subir la foto',
    });
  }

  removeFoto(): void {
    this.fotoPreview.set(null);
    this.form.patchValue({ foto_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Testimonio actualizado'); this.router.navigate(['/cenefco/testimonios']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
