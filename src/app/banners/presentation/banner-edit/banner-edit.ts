import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BannerService } from '../../application/services/banner.service';
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
  private http          = inject(HttpClient);
  private cdr           = inject(ChangeDetectorRef);

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
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el banner'));
        this.loadingBanner.set(false);
        this.router.navigate(['/cenefco/banners']);
        this.cdr.detectChanges();
      }
    });
  }

  onImagenSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => { this.imgPreview.set(e.target?.result as string); this.cdr.detectChanges(); };
    reader.readAsDataURL(file);

    this.uploadingImg.set(true);
    const formData = new FormData();
    formData.append('file', file);

    this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({
      next: (res) => {
        this.form.patchValue({ imagen_url: res.url });
        this.imgPreview.set(res.url);
        this.uploadingImg.set(false);
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir la imagen'));
        this.imgPreview.set(this.form.get('imagen_url')?.value || null);
        this.uploadingImg.set(false);
        input.value = '';
        this.cdr.detectChanges();
      }
    });
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
