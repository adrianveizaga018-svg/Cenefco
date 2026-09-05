import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DescargableService } from '../../application/services/descargable.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-descargable-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './descargable-create.html' })
export class DescargableCreate {
  private service = inject(DescargableService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);
  submitting = signal(false);

  uploadingArchivo = signal(false);
  archivoNombre    = signal<string | null>(null);
  uploadingImagen  = signal(false);
  imagenPreview    = signal<string | null>(null);

  form = this.fb.group({
    nombre:             ['', [Validators.required, Validators.maxLength(300)]],
    tipo:               [''],
    archivo_url:        ['', [Validators.required]],
    imagen_portada_url: [''],
    programa_id:        [null as number | null],
    requiere_datos:     [true],
    orden:              [0],
    activo:             [true],
  });

  onArchivoSelected(event: Event): void {
    this.fileUpload.handleFileSelect(event, {
      uploading:   this.uploadingArchivo,
      fileName:    this.archivoNombre,
      onSuccess:   (url) => this.form.patchValue({ archivo_url: url }),
      fallbackMsg: 'No se pudo subir el archivo',
    });
  }

  removeArchivo(): void {
    this.archivoNombre.set(null);
    this.form.patchValue({ archivo_url: '' });
  }

  onImagenSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.imagenPreview,
      uploading:   this.uploadingImagen,
      onSuccess:   (url) => this.form.patchValue({ imagen_portada_url: url }),
      fallbackMsg: 'No se pudo subir la imagen',
    });
  }

  removeImagen(): void {
    this.imagenPreview.set(null);
    this.form.patchValue({ imagen_portada_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingArchivo() || this.uploadingImagen()) return;
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Descargable registrado'); this.router.navigate(['/cenefco/descargables']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
