import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { PopupService } from '../../application/services/popup.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-popup-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './popup-edit.html' })
export class PopupEdit {
  private service    = inject(PopupService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private route      = inject(ActivatedRoute);
  private fb         = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);

  submitting   = signal(false);
  loading      = signal(true);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);

  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    titulo: [''], contenido: [''], imagen_url: [''], enlace_url: [''], enlace_texto: [''],
    posicion: ['center'], delay_segundos: [3], mostrar_una_vez_sesion: [true],
    mostrar_una_vez_siempre: [false], paginas_mostrar: [''],
    activo: [false], fecha_inicio: [''], fecha_fin: [''],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue(d as any);
        if (d.imagen_url) this.imgPreview.set(d.imagen_url);
        this.loading.set(false);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/popups']); }
    });
  }

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
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Popup actualizado'); this.router.navigate(['/cenefco/popups']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
