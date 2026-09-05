import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { OrganigramaService } from '../../application/services/organigrama.service';
import { Organigrama } from '../../domain/models/organigrama.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-organigrama-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './organigrama-create.html',
  styles: ``
})
export class OrganigramaCreate {
  private fb                 = inject(FormBuilder);
  private organigramaService = inject(OrganigramaService);
  private toast              = inject(ToastService);
  private router             = inject(Router);

  submitting = signal(false);
  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.organigramaService.create(this.form.value as unknown as Partial<Organigrama>).subscribe({
      next: () => { this.toast.success('¡Creado!', 'El organigrama ha sido creado correctamente'); this.router.navigate(['/cenefco/organigramas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el organigrama')); this.submitting.set(false); }
    });
  }
}
