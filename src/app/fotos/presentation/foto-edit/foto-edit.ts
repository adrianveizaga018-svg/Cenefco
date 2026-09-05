import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FotoService } from '../../application/services/foto.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-foto-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './foto-edit.html' })
export class FotoEdit {
  private service = inject(FotoService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);

  submitting   = signal(false);
  loading      = signal(true);
  uploadingImg = signal(false);
  previewUrl   = signal<string | null>(null);

  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    titulo_foto:      ['', [Validators.required, Validators.maxLength(200)]],
    descripcion_foto: [''],
    foto:             [''],
    fecha_foto:       [''],
    estado:           [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue(d as any);
        if (d.foto) this.previewUrl.set(d.foto);
        this.loading.set(false);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/fotos']); }
    });
  }

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
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'Foto actualizada'); this.router.navigate(['/cenefco/fotos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
