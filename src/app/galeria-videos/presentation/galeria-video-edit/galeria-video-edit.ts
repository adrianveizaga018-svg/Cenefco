import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { GaleriaVideoService } from '../../application/services/galeria-video.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { extractVideoId, getYoutubeThumbnail } from '../../../utils/video-url';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-galeria-video-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './galeria-video-edit.html' })
export class GaleriaVideoEdit {
  private service = inject(GaleriaVideoService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    titulo: ['', [Validators.required]], descripcion: [''], plataforma: ['youtube'],
    url_video: ['', [Validators.required]], video_id: [''], miniatura_url: [''],
    duracion: [''], tipo: [''], programa_id: [null as number | null],
    destacado: [false], orden: [0], activo: [true],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/galeria-videos']); }
    });

    this.form.get('url_video')?.valueChanges.subscribe(() => this.autoDetectarId());
    this.form.get('plataforma')?.valueChanges.subscribe(() => this.autoDetectarId());
  }

  private autoDetectarId(): void {
    const plataforma = this.form.get('plataforma')?.value ?? '';
    const url = this.form.get('url_video')?.value ?? '';
    const id = extractVideoId(plataforma, url);
    if (id) this.form.get('video_id')?.setValue(id);
  }

  get previewThumbnail(): string | null {
    const miniatura = this.form.get('miniatura_url')?.value;
    if (miniatura) return miniatura;
    const plataforma = this.form.get('plataforma')?.value;
    const videoId = this.form.get('video_id')?.value;
    return plataforma === 'youtube' && videoId ? getYoutubeThumbnail(videoId) : null;
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Video actualizado'); this.router.navigate(['/cenefco/galeria-videos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
