import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { BannerService } from '../../application/services/banner.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-banner-edit',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './banner-edit.html',
  styles: ``
})
export class BannerEdit {
  private bannerService = inject(BannerService);
  private toast         = inject(ToastService);
  private router        = inject(Router);
  private route         = inject(ActivatedRoute);
  private fb            = inject(FormBuilder);
  private fileUpload    = inject(FileUploadService);

  submitting    = signal(false);
  loadingBanner = signal(true);
  uploadingImg  = signal(false);
  imgPreview    = signal<string | null>(null);
  bannerId      = signal<number | null>(null);
  private slug  = this.route.snapshot.paramMap.get('slug') ?? '';

  form: FormGroup = this.fb.group({
    titulo:         ['', [Validators.maxLength(200)]],
    descripcion:    [''],
    imagen_url:     ['', [Validators.required]],
    enlace_url:     [''],
    enlace_texto:   [''],
    enlace_url_2:   [''],
    enlace_texto_2: [''],
    fecha_inicio:   [''],
    fecha_fin:      [''],
    activo:         [true],
    orden:          [0],
  });

  constructor() {
    this.bannerService.getBySlug(this.slug).subscribe({
      next: (banner) => {
        this.bannerId.set(banner.id);
        this.form.patchValue({
          titulo:         banner.titulo         ?? '',
          descripcion:    banner.descripcion    ?? '',
          imagen_url:     banner.imagen_url,
          enlace_url:     banner.enlace_url     ?? '',
          enlace_texto:   banner.enlace_texto   ?? '',
          enlace_url_2:   banner.enlace_url_2   ?? '',
          enlace_texto_2: banner.enlace_texto_2 ?? '',
          fecha_inicio:   banner.fecha_inicio   ?? '',
          fecha_fin:      banner.fecha_fin      ?? '',
          activo:         banner.activo,
          orden:          banner.orden,
        });
        if (banner.imagen_url) this.imgPreview.set(banner.imagen_url);
        this.loadingBanner.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el banner'));
        this.loadingBanner.set(false);
        this.router.navigate(['/cenefco/banners']);
      }
    });
  }

  onImagenSelected(event: Event): void {
    const prevUrl = this.form.get('imagen_url')?.value || null;
    this.fileUpload.handleImageSelect(event, {
      preview:     this.imgPreview,
      uploading:   this.uploadingImg,
      onSuccess:   (url) => { this.form.patchValue({ imagen_url: url }); this.imgPreview.set(url); },
      fallbackMsg: 'No se pudo subir la imagen',
    });
    // Restaurar preview a la URL guardada si el upload falla (manejado por handleImageSelect devolviendo null)
    void prevUrl;
  }

  removeImagen(): void {
    this.imgPreview.set(null);
    this.form.patchValue({ imagen_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingImg()) return;

    const id = this.bannerId();
    if (!id) return;

    this.submitting.set(true);
    const val = this.form.value;
    this.bannerService.update(id, {
      ...val,
      fecha_inicio: val.fecha_inicio || null,
      fecha_fin:    val.fecha_fin    || null,
      orden:        Number(val.orden) || 0,
    }).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Banner actualizado exitosamente');
        this.router.navigate(['/cenefco/banners']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el banner'));
        this.submitting.set(false);
      }
    });
  }
}
