import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AyudaService } from '../../application/services/ayuda.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-ayuda-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './ayuda-create.html' })
export class AyudaCreate {
  private service = inject(AyudaService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_ayuda:              [this.autoId, [Validators.required]],
    num_ayuda:             [this.autoId, [Validators.required]],
    id_us:                 [null as number | null, [Validators.required]],
    gestion:               [''],
    monto_pagado:          [''],
    nro_recibo:            [''],
    fecha_recibo:          [''],
    observacion_pago:      [''],
    id_categoriatipoayuda: [null as number | null],
    estado:                [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Ayuda registrada'); this.router.navigate(['/cenefco/ayudas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
