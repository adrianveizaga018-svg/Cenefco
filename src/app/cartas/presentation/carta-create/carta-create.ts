import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { CartaService } from '../../application/services/carta.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-carta-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './carta-create.html' })
export class CartaCreate {
  private service = inject(CartaService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_carta:     [this.autoId, [Validators.required]],
    num_carta:    [this.autoId],
    id_us:        [null as number | null],
    id_plan:      [null as number | null],
    nombresenor:  [''],
    nombretitulo: [''],
    textocarta1:  [''],
    textocarta2:  [''],
    textocarta3:  [''],
    estado:       [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creada!', 'Carta registrada'); this.router.navigate(['/cenefco/cartas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
