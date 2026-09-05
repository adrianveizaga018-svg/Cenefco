import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CursoImagenes } from '../../../common/components/curso-imagenes/curso-imagenes';
import { AreaService } from '../../application/services/area.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FileUploadService } from '../../../common/application/services/file-upload.service';

@Component({
  selector: 'app-area-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule, FormsModule, CursoImagenes],
  templateUrl: './area-edit.html',
  styles: ``
})
export class AreaEdit {
  private fb         = inject(FormBuilder);
  private service    = inject(AreaService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private route      = inject(ActivatedRoute);
  private fileUpload = inject(FileUploadService);

  private id!: number;
  private slug = this.route.snapshot.paramMap.get('slug') ?? '';
  submitting    = signal(false);
  loading       = signal(true);
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
    this.service.getBySlug(this.slug).subscribe({
      next: area => {
        this.id = area.id;
        this.form.patchValue(area as any);
        this.galeriaUrls.set(area.galeria ?? []);
        if (area.logo_url) this.logoPreview.set(area.logo_url);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el área');
        this.router.navigate(['/cenefco/areas']);
      },
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

  onGaleriaChange(urls: string[]): void {
    this.galeriaUrls.set(urls);
  }

  onSubmit(): void {
    if (this.form.invalid || this.uploadingLogo()) return;
    this.submitting.set(true);
    this.service.update(this.id, { ...this.form.value as any, galeria: this.galeriaUrls() }).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'Área actualizada correctamente');
        this.router.navigate(['/cenefco/areas']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el área'));
        this.submitting.set(false);
      },
    });
  }
}
