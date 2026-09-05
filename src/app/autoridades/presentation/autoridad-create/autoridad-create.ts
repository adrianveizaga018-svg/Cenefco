import { Component, inject, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { AutoridadService } from '../../application/services/autoridad.service';
import { Autoridad } from '../../domain/models/autoridad.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-autoridad-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './autoridad-create.html',
  styles: ``
})
export class AutoridadCreate {
  private fb               = inject(FormBuilder);
  private autoridadService = inject(AutoridadService);
  private toast            = inject(ToastService);
  private router           = inject(Router);
  private http              = inject(HttpClient);

  submitting    = signal(false);
  uploadingFoto = signal(false);
  fotoPreview   = signal<string | null>(null);

  form = this.fb.group({
    nombre:             ['', [Validators.required, Validators.maxLength(150)]],
    apellido:           ['', [Validators.required, Validators.maxLength(150)]],
    cargo:              ['', [Validators.required, Validators.maxLength(150)]],
    perfil_profesional: [''],
    foto_url:           [''],
    orden:              [0, [Validators.required]],
    activo:             [true],
    publicado_web:      [false],
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
        this.fotoPreview.set(this.form.value.foto_url || null);
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
    this.autoridadService.create(this.form.value as unknown as Partial<Autoridad>).subscribe({
      next: () => { this.toast.success('¡Creada!', 'La autoridad ha sido creada correctamente'); this.router.navigate(['/cenefco/autoridades']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la autoridad')); this.submitting.set(false); }
    });
  }
}
