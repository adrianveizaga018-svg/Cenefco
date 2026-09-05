import { ChangeDetectorRef, Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaPremioService } from '../../application/services/trivia-premio.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-trivia-premio-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './trivia-premio-edit.html',
  styles: ``
})
export class TriviaPremioEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(TriviaPremioService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting     = signal(false);
  loadingPremio  = signal(true);
  uploadingImg   = signal(false);
  imgPreview     = signal<string | null>(null);
  private id!: number;

  form: FormGroup = this.fb.group({
    nombre:       ['', [Validators.required, Validators.maxLength(150)]],
    descripcion:  [''],
    tipo:         ['souvenir', [Validators.required]],
    imagen_url:   [''],
    costo_puntos: [100, [Validators.required, Validators.min(1)]],
    stock:        [null as number | null],
    orden:        [0],
    activo:       [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (premio) => {
        this.form.patchValue({
          nombre: premio.nombre,
          descripcion: premio.descripcion ?? '',
          tipo: premio.tipo,
          imagen_url: premio.imagen_url ?? '',
          costo_puntos: premio.costo_puntos,
          stock: premio.stock,
          orden: premio.orden,
          activo: premio.activo,
        });
        this.imgPreview.set(premio.imagen_url ?? null);
        this.loadingPremio.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el premio'));
        this.router.navigate(['/cenefco/trivia-premios']);
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
    this.service.update(this.id, {
      ...val,
      costo_puntos: Number(val.costo_puntos) || 0,
      stock: val.stock === '' || val.stock === null ? null : Number(val.stock),
      orden: Number(val.orden) || 0,
    }).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'El premio ha sido actualizado correctamente');
        this.router.navigate(['/cenefco/trivia-premios']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el premio'));
        this.submitting.set(false);
      }
    });
  }
}
