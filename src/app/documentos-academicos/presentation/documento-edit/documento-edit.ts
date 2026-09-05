import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { DocumentoAcademicoService } from '../../application/services/documento-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-documento-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './documento-edit.html',
})
export class DocumentoEdit {
  private service = inject(DocumentoAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  loading    = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    id_fechapago:          [null as number | null],
    id_fechadoc:           [null as number | null],
    fecha_dejo_fisico:     [null as string | null],
    dejo_documento_fisico: [null as number | null],
    documento_digital:     [null as string | null],
    observacion_doc:       [null as string | null],
    estado:                [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: d => {
        this.form.patchValue(d as any);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar');
        this.router.navigate(['/cenefco/documentos-academicos']);
      },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Documento actualizado');
        this.router.navigate(['/cenefco/documentos-academicos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
        this.submitting.set(false);
      },
    });
  }
}
