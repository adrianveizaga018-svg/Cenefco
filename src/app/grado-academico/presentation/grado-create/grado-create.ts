import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { GradoAcademicoService } from '../../application/services/grado-academico.service';
import { GradoAcademico } from '../../domain/models/grado-academico.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-grado-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './grado-create.html',
  styles: ``
})
export class GradoCreate {
  private fb      = inject(FormBuilder);
  private service = inject(GradoAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  submitting = signal(false);

  form = this.fb.group({
    nombre:          ['', [Validators.required, Validators.maxLength(100)]],
    abreviatura:     ['', [Validators.required, Validators.maxLength(20)]],
    requiere_titulo: [false],
    orden:           [0],
    activo:          [true],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.create(this.form.value as Partial<GradoAcademico>).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Grado académico creado correctamente');
        this.router.navigate(['/cenefco/grados-academicos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el grado académico'));
        this.submitting.set(false);
      }
    });
  }
}
