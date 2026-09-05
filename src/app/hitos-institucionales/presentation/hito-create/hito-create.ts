import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { HitoInstitucionalService } from '../../application/services/hito-institucional.service';
import { HitoInstitucional } from '../../domain/models/hito-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FileUploadService } from '../../../common/application/services/file-upload.service';

@Component({
  selector: 'app-hito-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './hito-create.html',
  styles: ``
})
export class HitoCreate {
  private fb          = inject(FormBuilder);
  private service     = inject(HitoInstitucionalService);
  private toast       = inject(ToastService);
  private router      = inject(Router);
  private fileUpload  = inject(FileUploadService);

  submitting    = signal(false);
  uploadingImg  = signal(false);
  imgPreview    = signal<string | null>(null);

  form = this.fb.group({
    anio:        ['', [Validators.required]],
    titulo:      ['', [Validators.required]],
    descripcion: [''],
    imagen_url:  [''],
    imagen_alt:  [''],
    orden:       [0],
    activo:      [true],
  });

  onImgSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.imgPreview,
      uploading:   this.uploadingImg,
      onSuccess:   (url) => this.form.patchValue({ imagen_url: url }),
      fallbackMsg: 'No se pudo subir la imagen',
    });
  }

  removeImg(): void {
    this.imgPreview.set(null);
    this.form.patchValue({ imagen_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    if (this.uploadingImg()) return;
    this.submitting.set(true);
    this.service.create(this.form.value as Partial<HitoInstitucional>).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Hito institucional creado correctamente');
        this.router.navigate(['/cenefco/hitos-institucionales']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el hito'));
        this.submitting.set(false);
      }
    });
  }
}
