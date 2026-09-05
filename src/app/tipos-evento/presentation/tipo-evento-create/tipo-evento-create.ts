import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TipoEventoService } from '../../application/services/tipo-evento.service';
import { TipoEvento } from '../../domain/models/tipo-evento.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-evento-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './tipo-evento-create.html',
  styles: ``
})
export class TipoEventoCreate {
  private fb               = inject(FormBuilder);
  private tipoEventoService = inject(TipoEventoService);
  private toast            = inject(ToastService);
  private router           = inject(Router);

  submitting = signal(false);
  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.tipoEventoService.create(this.form.value as unknown as Partial<TipoEvento>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El tipo de evento ha sido creado correctamente'); this.router.navigate(['/cenefco/tipos-evento']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el tipo de evento')); this.submitting.set(false); }
    });
  }
}
