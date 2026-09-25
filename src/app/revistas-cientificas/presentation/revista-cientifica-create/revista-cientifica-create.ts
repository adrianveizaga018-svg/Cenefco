import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { RevistaCientificaService } from '../../application/services/revista-cientifica.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-revista-cientifica-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './revista-cientifica-create.html',
})
export class RevistaCientificaCreate {
  private service = inject(RevistaCientificaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);

  submitting    = signal(false);
  uploadingFile = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_revistacientifica:          [this.autoId, [Validators.required]],
    num_revistacientifica:         [this.autoId, [Validators.required]],
    titulo_revistacientifica:      ['', [Validators.required, Validators.maxLength(200)]],
    descripcion_revistacientifica: [''],
    fecha_publicacion:             [''],
    archivo:                       [''],
    estado:                        [1],
  });

  onArchivoSelected(event: Event): void {
    this.fileUpload.handleFileSelect(event, {
      uploading:   this.uploadingFile,
      fallbackMsg: 'No se pudo subir el archivo',
      onSuccess:   (url) => this.form.patchValue({ archivo: url }),
    });
  }

  removeArchivo(): void { this.form.patchValue({ archivo: '' }); }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Revista científica registrada'); this.router.navigate(['/cenefco/revistas-cientificas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
