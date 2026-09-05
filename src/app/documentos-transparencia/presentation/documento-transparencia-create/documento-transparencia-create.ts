import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DocumentoTransparenciaService } from '../../application/services/documento-transparencia.service';
import { DocumentoTransparencia } from '../../domain/models/documento-transparencia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-documento-transparencia-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './documento-transparencia-create.html',
  styles: ``
})
export class DocumentoTransparenciaCreate {
  private fb                            = inject(FormBuilder);
  private documentoTransparenciaService  = inject(DocumentoTransparenciaService);
  private toast                         = inject(ToastService);
  private router                        = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    estado: ['activo', [Validators.required]],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.documentoTransparenciaService.create(this.form.value as unknown as Partial<DocumentoTransparencia>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El documento ha sido creado correctamente'); this.router.navigate(['/cenefco/documentos-transparencia']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el documento')); this.submitting.set(false); }
    });
  }
}
