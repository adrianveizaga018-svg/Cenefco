import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { DocentePerfilService } from '../../application/services/docente-perfil.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-docente-perfil-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './docente-perfil-create.html' })
export class DocentePerfilCreate {
  private service    = inject(DocentePerfilService); private toast = inject(ToastService);
  private router     = inject(Router); private fb = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);
  submitting    = signal(false);
  uploadingFoto = signal(false);
  fotoPreview   = signal<string | null>(null);

  form = this.fb.group({
    nombre_completo:  ['', [Validators.required, Validators.maxLength(300)]],
    titulo_academico: [''],
    especialidad:     [''],
    biografia:        [''],
    foto_url:         [''],
    foto_alt:         [''],
    email_publico:    [''],
    telefono:         [''],
    linkedin_url:     [''],
    twitter_url:      [''],
    sitio_web_url:    [''],
    tipo:             ['docente'],
    mostrar_en_web:   [true],
    orden:            [0],
    estado:           ['publicado'],
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
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Docente registrado'); this.router.navigate(['/cenefco/docentes-perfil']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
