import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FechaDocService } from '../../application/services/fecha-doc.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-fecha-doc-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './fecha-doc-create.html' })
export class FechaDocCreate {
  private service = inject(FechaDocService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);
  form = this.fb.group({
    id_fechadoc: [this.autoId, [Validators.required]],
    id_plandoc:  [null as number | null, [Validators.required]],
    nro_doc: [''], tipo_documento: [''], fecha_inicio: [''], fecha_fin: [''], obligatorio: [0], estado: [1],
  });
  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('Creada', 'Fecha registrada'); this.router.navigate(['/cenefco/fechas-doc']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err)); this.submitting.set(false); }
    });
  }
}
