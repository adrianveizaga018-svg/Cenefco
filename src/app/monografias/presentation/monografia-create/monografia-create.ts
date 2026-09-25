import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { MonografiaService } from '../../application/services/monografia.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-monografia-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './monografia-create.html',
})
export class MonografiaCreate {
  private service = inject(MonografiaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);

  submitting    = signal(false);
  uploadingFile = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_monografia:          [this.autoId, [Validators.required]],
    num_monografia:         [this.autoId, [Validators.required]],
    titulo_monografia:      ['', [Validators.required, Validators.maxLength(200)]],
    descripcion_monografia: [''],
    autor:                  ['', [Validators.maxLength(200)]],
    fecha_publicacion:      [''],
    archivo:                [''],
    estado:                 [1],
  });

  onArchivoSelected(event: Event): void {
    this.fileUpload.handleFileSelect(event, {
      uploading:   this.uploadingFile,
      fallbackMsg: 'No se pudo subir',
      onSuccess:   (url) => this.form.patchValue({ archivo: url }),
    });
  }

  removeArchivo(): void { this.form.patchValue({ archivo: '' }); }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Monografía registrada'); this.router.navigate(['/cenefco/monografias']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
