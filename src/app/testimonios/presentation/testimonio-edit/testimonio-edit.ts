import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { TestimonioService } from '../../application/services/testimonio.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-testimonio-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './testimonio-edit.html' })
export class TestimonioEdit {
  private service = inject(TestimonioService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);

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
        const prev = this.form.value.foto_url;
        this.fotoPreview.set(prev
          ? (prev.startsWith('http') || prev.startsWith('/storage') ? prev : `/storage/${prev}`)
          : null
        );
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
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Testimonio actualizado'); this.router.navigate(['/cenefco/testimonios']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
