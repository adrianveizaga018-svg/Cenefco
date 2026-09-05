import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { DocumentoAcademicoService } from '../../application/services/documento-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-documento-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './documento-create.html',
})
export class DocumentoCreate {
  private service = inject(DocumentoAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_documento:          [this.autoId, [Validators.required]],
    id_us:                 [null as number | null, [Validators.required]],
    id_fechapago:          [null as number | null],
    id_fechadoc:           [null as number | null],
    fecha_dejo_fisico:     [null as string | null],
    dejo_documento_fisico: [null as number | null],
    documento_digital:     [null as string | null],
    observacion_doc:       [null as string | null],
    estado:                [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Documento registrado');
        this.router.navigate(['/cenefco/documentos-academicos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }
}
