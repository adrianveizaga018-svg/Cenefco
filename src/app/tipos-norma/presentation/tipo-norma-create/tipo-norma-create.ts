import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TipoNormaService } from '../../application/services/tipo-norma.service';
import { TipoNorma } from '../../domain/models/tipo-norma.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-norma-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './tipo-norma-create.html',
  styles: ``
})
export class TipoNormaCreate {
  private fb               = inject(FormBuilder);
  private tipoNormaService = inject(TipoNormaService);
  private toast            = inject(ToastService);
  private router           = inject(Router);

  submitting = signal(false);
  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.tipoNormaService.create(this.form.value as unknown as Partial<TipoNorma>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El tipo de norma ha sido creado correctamente'); this.router.navigate(['/cenefco/tipos-norma']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el tipo de norma')); this.submitting.set(false); }
    });
  }
}
