import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CifraInstitucionalService } from '../../application/services/cifra-institucional.service';
import { CifraInstitucional } from '../../domain/models/cifra-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-cifra-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './cifra-create.html',
  styles: ``
})
export class CifraCreate {
  private fb      = inject(FormBuilder);
  private service = inject(CifraInstitucionalService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  submitting = signal(false);

  form = this.fb.group({
    valor:       ['', [Validators.required]],
    etiqueta:    ['', [Validators.required]],
    descripcion: [''],
    icono:       [''],
    color:       [''],
    orden:       [0],
    activo:      [true],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.create(this.form.value as Partial<CifraInstitucional>).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'Cifra institucional creada correctamente');
        this.router.navigate(['/cenefco/cifras-institucionales']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la cifra'));
        this.submitting.set(false);
      }
    });
  }
}
