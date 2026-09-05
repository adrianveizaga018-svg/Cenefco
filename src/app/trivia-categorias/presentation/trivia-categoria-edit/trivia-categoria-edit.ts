import { ChangeDetectorRef, Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaCategoriaService } from '../../application/services/trivia-categoria.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-trivia-categoria-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './trivia-categoria-edit.html',
  styles: ``
})
export class TriviaCategoriaEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(TriviaCategoriaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting        = signal(false);
  loadingCategoria  = signal(true);
  uploadingImg      = signal(false);
  imgPreview        = signal<string | null>(null);
  private id!: number;

  form: FormGroup = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(150)]],
    descripcion: [''],
    imagen_url:  [''],
    color:       ['#7c3aed'],
    curso_id:    [null as number | null],
    orden:       [0],
    activo:      [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (categoria) => {
        this.form.patchValue({
          nombre: categoria.nombre,
          descripcion: categoria.descripcion ?? '',
          imagen_url: categoria.imagen_url ?? '',
          color: categoria.color ?? '#7c3aed',
          curso_id: categoria.curso_id ?? null,
          orden: categoria.orden,
          activo: categoria.activo,
        });
        this.imgPreview.set(categoria.imagen_url ?? null);
        this.loadingCategoria.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la categoría'));
        this.router.navigate(['/cenefco/trivia-categorias']);
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
      curso_id: val.curso_id || null,
      orden: Number(val.orden) || 0,
    }).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'La categoría ha sido actualizada correctamente');
        this.router.navigate(['/cenefco/trivia-categorias']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la categoría'));
        this.submitting.set(false);
      }
    });
  }
}
