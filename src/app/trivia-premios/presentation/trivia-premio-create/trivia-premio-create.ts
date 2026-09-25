import { Component, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaPremioService } from '../../application/services/trivia-premio.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-trivia-premio-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './trivia-premio-create.html',
  styles: ``
})
export class TriviaPremioCreate {
  private fb         = inject(FormBuilder);
  private service    = inject(TriviaPremioService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private fileUpload = inject(FileUploadService);

  submitting   = signal(false);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);

  form: FormGroup = this.fb.group({
    nombre:       ['', [Validators.required, Validators.maxLength(150)]],
    descripcion:  [''],
    tipo:         ['souvenir', [Validators.required]],
    imagen_url:   [''],
    costo_puntos: [100, [Validators.required, Validators.min(1)]],
    stock:        [null as number | null],
    orden:        [0],
    activo:       [true],
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
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingImg()) return;

    this.submitting.set(true);
    const val = this.form.value;
    this.service.create({
      ...val,
      costo_puntos: Number(val.costo_puntos) || 0,
      stock: val.stock === '' || val.stock === null ? null : Number(val.stock),
      orden: Number(val.orden) || 0,
    }).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'El premio ha sido creado correctamente');
        this.router.navigate(['/cenefco/trivia-premios']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el premio'));
        this.submitting.set(false);
      }
    });
  }
}
