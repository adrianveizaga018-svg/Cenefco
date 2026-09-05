import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { FaqService } from '../../application/services/faq.service';
import { Faq } from '../../domain/models/faq.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-faq-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './faq-edit.html',
  styles: ``
})
export class FaqEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(FaqService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting = signal(false);
  loading    = signal(true);
  private id!: number;

  form = this.fb.group({
    pregunta:    ['', [Validators.required]],
    respuesta:   ['', [Validators.required]],
    categoria:   [''],
    programa_id: [null as number | null],
    orden:       [0],
    activo:      [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.form.patchValue({
          pregunta:    item.pregunta,
          respuesta:   item.respuesta,
          categoria:   item.categoria ?? '',
          programa_id: item.programa_id,
          orden:       item.orden,
          activo:      item.activo,
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la FAQ'));
        this.router.navigate(['/cenefco/faqs']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as Partial<Faq>).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'FAQ actualizada correctamente');
        this.router.navigate(['/cenefco/faqs']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la FAQ'));
        this.submitting.set(false);
      }
    });
  }
}
