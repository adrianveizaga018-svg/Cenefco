import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { EventoService } from '../../application/services/evento.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-evento-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './evento-create.html',
  styles: ``
})
export class EventoCreate {
  private eventoService = inject(EventoService);
  private toast         = inject(ToastService);
  private router        = inject(Router);
  private fb            = inject(FormBuilder);

  submitting   = signal(false);
  uploadingImg = signal(false);
  previewUrl   = signal<string | null>(null);

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

    this.submitting.set(true);
    this.eventoService.create(this.form.value).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Evento registrado exitosamente');
        this.router.navigate(['/cenefco/eventos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar el evento'));
        this.submitting.set(false);
      }
    });
  }
}
