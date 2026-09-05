import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { CartaModeloService } from '../../application/services/carta-modelo.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-carta-modelo-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './carta-modelo-create.html' })
export class CartaModeloCreate {
  private service = inject(CartaModeloService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_cartamod:                  [this.autoId, [Validators.required]],
    num_cartamod:                 [this.autoId],
    nombremodelo:                 ['', [Validators.required, Validators.maxLength(200)]],
    textocarta:                   [''],
    textocarta1:                  [''],
    textocarta3:                  [''],
    texto_carta:                  [''],
    usar_encabezado_pie_estandar: [1],
    estado:                       [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Modelo de carta registrado'); this.router.navigate(['/cenefco/cartas-modelo']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
