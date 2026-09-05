import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { TestimonioService } from '../../application/services/testimonio.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-testimonio-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './testimonio-create.html' })
export class TestimonioCreate {
  private service = inject(TestimonioService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);

  submitting    = signal(false);
  uploadingFoto = signal(false);
  fotoPreview   = signal<string | null>(null);

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

  onFotoSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => this.fotoPreview.set(e.target?.result as string);
    reader.readAsDataURL(file);

    this.uploadingFoto.set(true);
    const formData = new FormData();
    formData.append('file', file);

    this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({
      next: (res) => { this.form.patchValue({ foto_url: res.url }); this.uploadingFoto.set(false); },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir la foto'));
        this.fotoPreview.set(null);
        this.uploadingFoto.set(false);
        input.value = '';
      }
    });
  }

  removeFoto(): void {
    this.fotoPreview.set(null);
    this.form.patchValue({ foto_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Testimonio registrado'); this.router.navigate(['/cenefco/testimonios']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
