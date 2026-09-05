import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CursoImagenes } from '../../../common/components/curso-imagenes/curso-imagenes';
import { AreaService } from '../../application/services/area.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { generateSlug } from '../../../utils/slug';

@Component({
  selector: 'app-area-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule, FormsModule, CursoImagenes],
  templateUrl: './area-create.html',
  styles: ``
})
export class AreaCreate {
  private fb          = inject(FormBuilder);
  private service     = inject(AreaService);
  private toast       = inject(ToastService);
  private router      = inject(Router);
  private fileUpload  = inject(FileUploadService);

  submitting    = signal(false);
  uploadingLogo = signal(false);
  logoPreview   = signal<string | null>(null);
  galeriaUrls   = signal<string[]>([]);

  form = this.fb.group({
    titulo:           ['', [Validators.required, Validators.maxLength(200)]],
    slug:             [''],
    descripcion:      [''],
    logo_url:         [''],
    logo_alt:         [''],
    color:            ['#6366f1'],
    icono:            ['lucideShapes'],
    orden:            [0],
    activo:           [true],
    meta_titulo:      [''],
    meta_descripcion: [''],
  });

  constructor() {
    this.form.get('titulo')!.valueChanges.subscribe((titulo: string | null) => {
      this.form.get('slug')!.setValue(generateSlug(titulo ?? ''), { emitEvent: false });
    });
  }

  onLogoSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:    this.logoPreview,
      uploading:  this.uploadingLogo,
      onSuccess:  (url) => this.form.patchValue({ logo_url: url }),
      fallbackMsg: 'No se pudo subir el logo',
    });
  }

  removeLogo(): void {
    this.logoPreview.set(null);
    this.form.patchValue({ logo_url: '' });
  }

  onGaleriaChange(urls: string[]): void {
    this.galeriaUrls.set(urls);
  }

  onSubmit(): void {
    if (this.form.invalid || this.uploadingLogo()) return;
    this.submitting.set(true);
    this.service.create({ ...this.form.value as any, galeria: this.galeriaUrls() }).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'Área creada correctamente');
        this.router.navigate(['/cenefco/areas']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el área'));
        this.submitting.set(false);
      },
    });
  }
}
