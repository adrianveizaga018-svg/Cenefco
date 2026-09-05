import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { TesisService } from '../../application/services/tesis.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tesis-edit',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './tesis-edit.html',
})
export class TesisEdit {
  private service = inject(TesisService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting    = signal(false);
  loading       = signal(true);
  uploadingFile = signal(false);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    titulo_tesis:      ['', [Validators.required, Validators.maxLength(200)]],
    descripcion_tesis: [''],
    autor:             ['', [Validators.maxLength(200)]],
    fecha_publicacion: [''],
    tipo_tesis:        [null as number | null],
    archivo:           [''],
    estado:            [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/tesis']); },
    });
  }

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
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Tesis actualizada'); this.router.navigate(['/cenefco/tesis']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); },
    });
  }
}
