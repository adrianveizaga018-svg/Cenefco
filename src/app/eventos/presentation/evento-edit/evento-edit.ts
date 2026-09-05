import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { EventoService } from '../../application/services/evento.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-evento-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './evento-edit.html',
  styles: ``
})
export class EventoEdit {
  private eventoService = inject(EventoService);
  private toast         = inject(ToastService);
  private router        = inject(Router);
  private route         = inject(ActivatedRoute);
  private fb            = inject(FormBuilder);

  submitting    = signal(false);
  loadingEvento = signal(true);
  uploadingImg  = signal(false);
  previewUrl    = signal<string | null>(null);
  eventoId      = signal<number | null>(null);

  readonly tiposEvento = this.eventoService.tiposEvento;

  form: FormGroup = this.fb.group({
    tipo:            [null, [Validators.required]],
    titulo:          ['', [Validators.required, Validators.maxLength(300)]],
    descripcion:     [''],
    imagen_url:      [''],
    lugar:           [''],
    fecha_inicio:    ['', [Validators.required]],
    fecha_fin:       [''],
    todo_el_dia:     [false],
    estado:          ['programado'],
    url_transmision: [''],
    gratuito:        [true],
  });

  constructor() {
    const slug = this.route.snapshot.paramMap.get('slug') ?? '';

    this.eventoService.getBySlug(slug).subscribe({
      next: (evento) => {
        this.eventoId.set(evento.id);
        this.form.patchValue({
          tipo:            evento.tipo ?? null,
          titulo:          evento.titulo,
          descripcion:     evento.descripcion ?? '',
          imagen_url:      evento.imagen_url ?? '',
          lugar:           evento.lugar ?? '',
          fecha_inicio:    evento.fecha_inicio ? evento.fecha_inicio.substring(0, 10) : '',
          fecha_fin:       evento.fecha_fin ? evento.fecha_fin.substring(0, 10) : '',
          todo_el_dia:     evento.todo_el_dia,
          estado:          evento.estado,
          url_transmision: evento.url_transmision ?? '',
          gratuito:        evento.gratuito,
        });
        if (evento.imagen_url) this.previewUrl.set(evento.imagen_url);
        this.loadingEvento.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el evento'));
        this.loadingEvento.set(false);
        this.router.navigate(['/cenefco/eventos']);
      }
    });
  }

  onImageSelected(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => this.previewUrl.set(e.target?.result as string);
    reader.readAsDataURL(file);

    this.uploadingImg.set(true);
    this.eventoService.uploadImage(file).subscribe({
      next: (res) => { this.form.patchValue({ imagen_url: res.url }); this.uploadingImg.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo subir la imagen'); this.previewUrl.set(null); this.uploadingImg.set(false); }
    });
  }

  removeImage(): void {
    this.previewUrl.set(null);
    this.form.patchValue({ imagen_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingImg()) return;

    const id = this.eventoId();
    if (!id) return;

    this.submitting.set(true);
    this.eventoService.update(id, this.form.value).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Evento actualizado exitosamente');
        this.router.navigate(['/cenefco/eventos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el evento'));
        this.submitting.set(false);
      }
    });
  }
}
