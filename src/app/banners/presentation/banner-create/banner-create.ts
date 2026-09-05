import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BannerService } from '../../application/services/banner.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-banner-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './banner-create.html',
  styles: ``
})
export class BannerCreate {
  private bannerService = inject(BannerService);
  private toast         = inject(ToastService);
  private router        = inject(Router);
  private fb            = inject(FormBuilder);
  private http          = inject(HttpClient);
  private cdr           = inject(ChangeDetectorRef);

  submitting   = signal(false);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);

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
        this.imgPreview.set(null);
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

    this.submitting.set(true);
    const val = this.form.value;
    this.bannerService.create({
      ...val,
      fecha_inicio: val.fecha_inicio || null,
      fecha_fin:    val.fecha_fin    || null,
      orden:        Number(val.orden) || 0,
    }).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Banner registrado exitosamente');
        this.router.navigate(['/cenefco/banners']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar el banner'));
        this.submitting.set(false);
      }
    });
  }
}
