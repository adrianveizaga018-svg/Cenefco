import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { CartaService } from '../../application/services/carta.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-carta-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './carta-edit.html' })
export class CartaEdit {
  private service = inject(CartaService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    nombresenor:  [''],
    nombretitulo: [''],
    id_us:        [null as number | null],
    id_plan:      [null as number | null],
    textocarta1:  [''],
    textocarta2:  [''],
    textocarta3:  [''],
    estado:       [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/cartas']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'Carta actualizada'); this.router.navigate(['/cenefco/cartas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
