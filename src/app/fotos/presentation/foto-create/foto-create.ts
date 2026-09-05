import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FotoService } from '../../application/services/foto.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-foto-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './foto-create.html' })
export class FotoCreate {
  private service = inject(FotoService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);

  submitting   = signal(false);
  uploadingImg = signal(false);
  previewUrl   = signal<string | null>(null);

  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_foto:          [this.autoId, [Validators.required]],
    num_foto:         [this.autoId, [Validators.required]],
    titulo_foto:      ['', [Validators.required, Validators.maxLength(200)]],
    descripcion_foto: [''],
    foto:             [''],
    fecha_foto:       [''],
    estado:           [1],
  });

  onImageSelected(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => this.previewUrl.set(e.target?.result as string);
    reader.readAsDataURL(file);
    this.uploadingImg.set(true);
    this.service.uploadImage(file).subscribe({
      next: (res) => { this.form.patchValue({ foto: res.url }); this.uploadingImg.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo subir la imagen'); this.previewUrl.set(null); this.uploadingImg.set(false); }
    });
  }

  removeImage(): void {
    this.previewUrl.set(null);
    this.form.patchValue({ foto: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingImg()) return;
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creada!', 'Foto registrada'); this.router.navigate(['/cenefco/fotos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
