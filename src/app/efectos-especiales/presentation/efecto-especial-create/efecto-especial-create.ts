import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { EfectoEspecialService } from '../../application/services/efecto-especial.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { TIPOS_EFECTO } from '../../domain/models/efecto-especial.model';

@Component({
  selector: 'app-efecto-especial-create',
  imports: [ReactiveFormsModule, RouterLink, PageTitle],
  templateUrl: './efecto-especial-create.html',
})
export class EfectoEspecialCreate {
  private service = inject(EfectoEspecialService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);

  readonly tipos = TIPOS_EFECTO;
  submitting     = signal(false);

  form = this.fb.group({
    nombre:           ['', [Validators.required, Validators.maxLength(100)]],
    tipo_efecto:      ['nieve', [Validators.required]],
    color_primario:   ['#ffffff'],
    color_secundario: [null as string | null],
    fecha_inicio:     ['', [Validators.required]],
    fecha_fin:        ['', [Validators.required]],
    intensidad:       [50, [Validators.required, Validators.min(0), Validators.max(100)]],
    activo:           [true],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Efecto especial guardado');
        this.router.navigate(['/cenefco/efectos-especiales']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }
}
