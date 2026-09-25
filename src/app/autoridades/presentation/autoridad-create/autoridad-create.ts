import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { AutoridadService } from '../../application/services/autoridad.service';
import { Autoridad } from '../../domain/models/autoridad.model';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
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
  private fileUpload       = inject(FileUploadService);

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
    this.autoridadService.create(this.form.value as unknown as Partial<Autoridad>).subscribe({
      next: () => { this.toast.success('¡Creada!', 'La autoridad ha sido creada correctamente'); this.router.navigate(['/cenefco/autoridades']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la autoridad')); this.submitting.set(false); }
    });
  }
}
