import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { EfectoEspecialService } from '../../application/services/efecto-especial.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { TIPOS_EFECTO } from '../../domain/models/efecto-especial.model';

@Component({
  selector: 'app-efecto-especial-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './efecto-especial-edit.html',
})
export class EfectoEspecialEdit implements OnInit {
  private service = inject(EfectoEspecialService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);

  readonly tipos = TIPOS_EFECTO;
  submitting     = signal(false);
  loading        = signal(true);
  id             = Number(this.route.snapshot.paramMap.get('id'));

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

  ngOnInit(): void {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue({
          nombre:           d.nombre,
          tipo_efecto:      d.tipo_efecto,
          color_primario:   d.color_primario ?? '#ffffff',
          color_secundario: d.color_secundario,
          fecha_inicio:     d.fecha_inicio?.slice(0, 10) ?? '',
          fecha_fin:        d.fecha_fin?.slice(0, 10) ?? '',
          intensidad:       d.intensidad,
          activo:           d.activo,
        });
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el efecto');
        this.router.navigate(['/cenefco/efectos-especiales']);
      },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Efecto actualizado correctamente');
        this.router.navigate(['/cenefco/efectos-especiales']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
        this.submitting.set(false);
      },
    });
  }
}
