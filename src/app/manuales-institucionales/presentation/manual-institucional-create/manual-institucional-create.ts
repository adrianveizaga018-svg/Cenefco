import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ManualInstitucionalService } from '../../application/services/manual-institucional.service';
import { ManualInstitucional } from '../../domain/models/manual-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-manual-institucional-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './manual-institucional-create.html',
  styles: ``
})
export class ManualInstitucionalCreate {
  private fb                         = inject(FormBuilder);
  private manualInstitucionalService  = inject(ManualInstitucionalService);
  private toast                      = inject(ToastService);
  private router                     = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.manualInstitucionalService.create(this.form.value as unknown as Partial<ManualInstitucional>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El manual ha sido creado correctamente'); this.router.navigate(['/cenefco/manuales-institucionales']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el manual')); this.submitting.set(false); }
    });
  }
}
