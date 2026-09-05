import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaCategoriaService } from '../../application/services/trivia-categoria.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-trivia-categoria-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './trivia-categoria-create.html',
  styles: ``
})
export class TriviaCategoriaCreate {
  private fb      = inject(FormBuilder);
  private service = inject(TriviaCategoriaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting   = signal(false);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);

  form: FormGroup = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(150)]],
    descripcion: [''],
    imagen_url:  [''],
    color:       ['#7c3aed'],
    curso_id:    [null as number | null],
    orden:       [0],
    activo:      [true],
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
    this.service.create({
      ...val,
      curso_id: val.curso_id || null,
      orden: Number(val.orden) || 0,
    }).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'La categoría ha sido creada correctamente');
        this.router.navigate(['/cenefco/trivia-categorias']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la categoría'));
        this.submitting.set(false);
      }
    });
  }
}
