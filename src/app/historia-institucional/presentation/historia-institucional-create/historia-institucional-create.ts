import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { HistoriaInstitucionalService } from '../../application/services/historia-institucional.service';
import { HistoriaInstitucional } from '../../domain/models/historia-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-historia-institucional-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './historia-institucional-create.html',
  styles: ``
})
export class HistoriaInstitucionalCreate {
  private fb                           = inject(FormBuilder);
  private historiaInstitucionalService  = inject(HistoriaInstitucionalService);
  private toast                        = inject(ToastService);
  private router                       = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.historiaInstitucionalService.create(this.form.value as unknown as Partial<HistoriaInstitucional>).subscribe({
      next: () => { this.toast.success('¡Creada!', 'La entrada ha sido creada correctamente'); this.router.navigate(['/cenefco/historia-institucional']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la entrada')); this.submitting.set(false); }
    });
  }
}
