import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ComunicadoService } from '../../application/services/comunicado.service';
import { Comunicado } from '../../domain/models/comunicado.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-comunicado-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './comunicado-create.html',
  styles: ``
})
export class ComunicadoCreate {
  private fb                = inject(FormBuilder);
  private comunicadoService = inject(ComunicadoService);
  private toast             = inject(ToastService);
  private router            = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    estado: ['borrador', [Validators.required]],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.comunicadoService.create(this.form.value as unknown as Partial<Comunicado>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El comunicado ha sido creado correctamente'); this.router.navigate(['/cenefco/comunicados']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el comunicado')); this.submitting.set(false); }
    });
  }
}
