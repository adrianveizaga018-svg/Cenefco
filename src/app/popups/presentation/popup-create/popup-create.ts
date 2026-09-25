import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { PopupService } from '../../application/services/popup.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-popup-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './popup-create.html' })
export class PopupCreate {
  private service    = inject(PopupService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private fb         = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);

  submitting   = signal(false);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);

  form = this.fb.group({
    titulo:                   [''],
    contenido:                [''],
    imagen_url:               [''],
    enlace_url:               [''],
    enlace_texto:             [''],
    posicion:                 ['center'],
    delay_segundos:           [3],
    mostrar_una_vez_sesion:   [true],
    mostrar_una_vez_siempre:  [false],
    paginas_mostrar:          [''],
    activo:                   [false],
    fecha_inicio:             [''],
    fecha_fin:                [''],
  });

  onImagenSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.imgPreview,
      uploading:   this.uploadingImg,
      onSuccess:   (url) => { this.form.patchValue({ imagen_url: url }); this.imgPreview.set(url); },
      fallbackMsg: 'No se pudo subir la imagen',
    });
  }

  removeImagen(): void {
    this.imgPreview.set(null);
    this.form.patchValue({ imagen_url: '' });
  }

  onSubmit(): void {
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Popup registrado'); this.router.navigate(['/cenefco/popups']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
