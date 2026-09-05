import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AcreditacionService } from '../../application/services/acreditacion.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-acreditacion-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './acreditacion-edit.html' })
export class AcreditacionEdit {
  private service = inject(AcreditacionService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  uploadingLogo = signal(false);
  logoPreview   = signal<string | null>(null);

  form = this.fb.group({
    nombre: ['', [Validators.required]], entidad_otorgante: ['', [Validators.required]],
    tipo: [''], descripcion: [''], logo_url: [''], logo_alt: [''],
    fecha_obtencion: [''], fecha_vencimiento: [''],
    orden: [0], activo: [true],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue(d as any);
        if ((d as any).logo_url) this.logoPreview.set((d as any).logo_url);
        this.loading.set(false);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/acreditaciones']); }
    });
  }

  onLogoSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.logoPreview,
      uploading:   this.uploadingLogo,
      onSuccess:   (url) => this.form.patchValue({ logo_url: url }),
      fallbackMsg: 'No se pudo subir el logo',
    });
  }

  removeLogo(): void {
    this.logoPreview.set(null);
    this.form.patchValue({ logo_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingLogo()) return;
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'Acreditación actualizada'); this.router.navigate(['/cenefco/acreditaciones']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
