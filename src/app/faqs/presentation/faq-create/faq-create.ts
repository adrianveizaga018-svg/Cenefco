import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { FaqService } from '../../application/services/faq.service';
import { Faq } from '../../domain/models/faq.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-faq-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './faq-create.html',
  styles: ``
})
export class FaqCreate {
  private fb      = inject(FormBuilder);
  private service = inject(FaqService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  submitting = signal(false);

  form = this.fb.group({
    pregunta:    ['', [Validators.required]],
    respuesta:   ['', [Validators.required]],
    categoria:   [''],
    programa_id: [null as number | null],
    orden:       [0],
    activo:      [true],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.create(this.form.value as Partial<Faq>).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'FAQ creada correctamente');
        this.router.navigate(['/cenefco/faqs']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la FAQ'));
        this.submitting.set(false);
      }
    });
  }
}
