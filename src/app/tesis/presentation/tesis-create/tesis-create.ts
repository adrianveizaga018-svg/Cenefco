import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { TesisService } from '../../application/services/tesis.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tesis-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './tesis-create.html',
})
export class TesisCreate {
  private service = inject(TesisService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting    = signal(false);
  uploadingFile = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_tesis:          [this.autoId, [Validators.required]],
    num_tesis:         [this.autoId, [Validators.required]],
    titulo_tesis:      ['', [Validators.required, Validators.maxLength(200)]],
    descripcion_tesis: [''],
    autor:             ['', [Validators.maxLength(200)]],
    fecha_publicacion: [''],
    tipo_tesis:        [null as number | null],
    archivo:           [''],
    estado:            [1],
  });

  onArchivoSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0];
    if (!file) return;

    this.uploadingFile.set(true);
    const fd = new FormData();
    fd.append('file', file);

    this.http.post<{ url: string }>('/api/v1/upload/file', fd).subscribe({
      next: (res) => { this.form.patchValue({ archivo: res.url }); this.uploadingFile.set(false); this.cdr.detectChanges(); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir el archivo')); this.uploadingFile.set(false); input.value = ''; this.cdr.detectChanges(); },
    });
  }

  removeArchivo(): void { this.form.patchValue({ archivo: '' }); }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Tesis registrada'); this.router.navigate(['/cenefco/tesis']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
