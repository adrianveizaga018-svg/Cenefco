import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TipoDocumentoTransparenciaService } from '../../application/services/tipo-documento-transparencia.service';
import { TipoDocumentoTransparencia } from '../../domain/models/tipo-documento-transparencia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-documento-transparencia-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './tipo-documento-transparencia-create.html',
  styles: ``
})
export class TipoDocumentoTransparenciaCreate {
  private fb                                   = inject(FormBuilder);
  private tipoDocumentoTransparenciaService     = inject(TipoDocumentoTransparenciaService);
  private toast                                = inject(ToastService);
  private router                               = inject(Router);

  submitting = signal(false);
  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.tipoDocumentoTransparenciaService.create(this.form.value as unknown as Partial<TipoDocumentoTransparencia>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El tipo de documento ha sido creado correctamente'); this.router.navigate(['/cenefco/tipos-documento-transparencia']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el tipo de documento')); this.submitting.set(false); }
    });
  }
}
