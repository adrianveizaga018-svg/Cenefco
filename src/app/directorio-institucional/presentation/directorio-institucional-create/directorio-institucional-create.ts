import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DirectorioInstitucionalService } from '../../application/services/directorio-institucional.service';
import { DirectorioInstitucional } from '../../domain/models/directorio-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-directorio-institucional-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './directorio-institucional-create.html',
  styles: ``
})
export class DirectorioInstitucionalCreate {
  private fb                             = inject(FormBuilder);
  private directorioInstitucionalService  = inject(DirectorioInstitucionalService);
  private toast                          = inject(ToastService);
  private router                         = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(150)]],
    cargo:  ['', [Validators.required, Validators.maxLength(150)]],
    orden:  [0, [Validators.required]],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.directorioInstitucionalService.create(this.form.value as unknown as Partial<DirectorioInstitucional>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'La entrada ha sido creada correctamente'); this.router.navigate(['/cenefco/directorio-institucional']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la entrada')); this.submitting.set(false); }
    });
  }
}
