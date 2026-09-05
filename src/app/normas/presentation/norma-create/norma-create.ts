import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { NormaService } from '../../application/services/norma.service';
import { Norma } from '../../domain/models/norma.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-norma-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './norma-create.html',
  styles: ``
})
export class NormaCreate {
  private fb           = inject(FormBuilder);
  private normaService = inject(NormaService);
  private toast        = inject(ToastService);
  private router       = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    estado: ['vigente', [Validators.required]],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.normaService.create(this.form.value as unknown as Partial<Norma>).subscribe({
      next: () => { this.toast.success('¡Creada!', 'La norma ha sido creada correctamente'); this.router.navigate(['/cenefco/normas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la norma')); this.submitting.set(false); }
    });
  }
}
