import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { PopupService } from '../../application/services/popup.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-popup-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './popup-edit.html' })
export class PopupEdit {
  private service = inject(PopupService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting   = signal(false);
  loading      = signal(true);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);

  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    titulo: [''], contenido: [''], imagen_url: [''], enlace_url: [''], enlace_texto: [''],
    posicion: ['center'], delay_segundos: [3], mostrar_una_vez_sesion: [true],
    mostrar_una_vez_siempre: [false], paginas_mostrar: [''],
    activo: [false], fecha_inicio: [''], fecha_fin: [''],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue(d as any);
        if (d.imagen_url) this.imgPreview.set(d.imagen_url);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/popups']); }
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
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Popup actualizado'); this.router.navigate(['/cenefco/popups']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
