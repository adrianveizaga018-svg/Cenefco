import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { DocentePerfilService } from '../../application/services/docente-perfil.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-docente-perfil-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './docente-perfil-edit.html' })
export class DocentePerfilEdit {
  private service = inject(DocentePerfilService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute);
  private fb      = inject(FormBuilder); private http = inject(HttpClient);

  submitting    = signal(false);
  loading       = signal(true);
  uploadingFoto = signal(false);
  fotoPreview   = signal<string | null>(null);
  private id!: number;
  private slug = this.route.snapshot.paramMap.get('slug') ?? '';

  form = this.fb.group({
    nombre_completo: ['', [Validators.required]], titulo_academico: [''], especialidad: [''],
    biografia: [''], foto_url: [''], foto_alt: [''], email_publico: [''], telefono: [''],
    linkedin_url: [''], twitter_url: [''], sitio_web_url: [''],
    tipo: ['docente'], mostrar_en_web: [true], orden: [0], estado: ['publicado'],
  });

  constructor() {
    this.service.getBySlug(this.slug).subscribe({
      next: (d) => {
        this.id = d.id;
        this.form.patchValue(d as any);
        if (d.foto_url) this.fotoPreview.set(d.foto_url);
        this.loading.set(false);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/docentes-perfil']); }
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
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Docente actualizado'); this.router.navigate(['/cenefco/docentes-perfil']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
