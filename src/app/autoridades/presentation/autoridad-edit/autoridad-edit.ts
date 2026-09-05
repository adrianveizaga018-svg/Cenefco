import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { AutoridadService } from '../../application/services/autoridad.service';
import { Autoridad } from '../../domain/models/autoridad.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-autoridad-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './autoridad-edit.html',
  styles: ``
})
export class AutoridadEdit implements OnInit {
  private fb               = inject(FormBuilder);
  private autoridadService = inject(AutoridadService);
  private toast            = inject(ToastService);
  private router           = inject(Router);
  private route            = inject(ActivatedRoute);
  private http              = inject(HttpClient);

  submitting       = signal(false);
  loadingAutoridad = signal(true);
  uploadingFoto    = signal(false);
  fotoPreview      = signal<string | null>(null);
  private id!: number;

  form = this.fb.group({
    nombre:             ['', [Validators.required, Validators.maxLength(150)]],
    apellido:           ['', [Validators.required, Validators.maxLength(150)]],
    cargo:              ['', [Validators.required, Validators.maxLength(150)]],
    perfil_profesional: [''],
    foto_url:           [''],
    orden:              [0, [Validators.required]],
    activo:             [true],
    publicado_web:      [false],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.autoridadService.getById(this.id).subscribe({
      next: (a) => {
        this.form.patchValue({
          nombre:             a.nombre,
          apellido:           a.apellido,
          cargo:              a.cargo,
          perfil_profesional: a.perfil_profesional ?? '',
          foto_url:           a.foto_url ?? '',
          orden:              a.orden,
          activo:             a.activo,
          publicado_web:      a.publicado_web,
        });
        if (a.foto_url) this.fotoPreview.set(a.foto_url);
        this.loadingAutoridad.set(false);
      },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la autoridad')); this.router.navigate(['/cenefco/autoridades']); }
    });
  }

  onFotoSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => this.fotoPreview.set(e.target?.result as string);
    reader.readAsDataURL(file);

    this.uploadingFoto.set(true);
    const formData = new FormData();
    formData.append('file', file);

    this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({
      next: (res) => { this.form.patchValue({ foto_url: res.url }); this.uploadingFoto.set(false); },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir la foto'));
        this.fotoPreview.set(this.form.value.foto_url || null);
        this.uploadingFoto.set(false);
        input.value = '';
      }
    });
  }

  removeFoto(): void {
    this.fotoPreview.set(null);
    this.form.patchValue({ foto_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.autoridadService.update(this.id, this.form.value as unknown as Partial<Autoridad>).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'La autoridad ha sido actualizada correctamente'); this.router.navigate(['/cenefco/autoridades']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la autoridad')); this.submitting.set(false); }
    });
  }
}
