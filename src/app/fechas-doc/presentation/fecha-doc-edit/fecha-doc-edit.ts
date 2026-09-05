import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FechaDocService } from '../../application/services/fecha-doc.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-fecha-doc-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './fecha-doc-edit.html' })
export class FechaDocEdit {
  private service = inject(FechaDocService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));
  form = this.fb.group({ nro_doc: [''], tipo_documento: [''], fecha_inicio: [''], fecha_fin: [''], obligatorio: [0], estado: [1] });
  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/fechas-doc']); }
    });
  }
  onSubmit(): void {
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('Actualizada', 'Fecha actualizada'); this.router.navigate(['/cenefco/fechas-doc']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err)); this.submitting.set(false); }
    });
  }
}
