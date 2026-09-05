import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { AliadoService } from '../../application/services/aliado.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'app-aliado-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './aliado-edit.html',
})
export class AliadoEdit {
  private service = inject(AliadoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);

  submitting = signal(false);
  loading    = signal(true);
  id         = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(200)]],
    logo_url:    ['', [Validators.required, Validators.maxLength(255)]],
    logo_alt:    ['', [Validators.maxLength(255)]],
    url_sitio:   ['', [Validators.maxLength(255)]],
    descripcion: ['', [Validators.maxLength(500)]],
    tipo:        ['', [Validators.maxLength(100)]],
    orden:       [0],
    activo:      [true],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (data) => {
        this.form.patchValue(data as any);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el aliado');
        this.router.navigate(['/cenefco/aliados']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Aliado actualizado exitosamente');
        this.router.navigate(['/cenefco/aliados']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el aliado'));
        this.submitting.set(false);
      }
    });
  }
}
