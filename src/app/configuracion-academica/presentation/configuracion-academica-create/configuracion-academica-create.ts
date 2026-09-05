import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ConfiguracionAcademicaService } from '../../application/services/configuracion-academica.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-configuracion-academica-create',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './configuracion-academica-create.html',
})
export class ConfiguracionAcademicaCreate {
  private service = inject(ConfiguracionAcademicaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_conf:                [this.autoId, [Validators.required]],
    gestion:                ['', [Validators.maxLength(10)]],
    periodo_est:            [''],
    gestion_est:            [''],
    max_materias_cursar:    [''],
    id_plan:                [null as number | null],
    id_plan_anterior:       [null as number | null],
    periodo_doc:            [''],
    gestion_doc:            [''],
    correlativo:            [''],
    nombre_kardista:        [''],
    nombre_director:        [''],
    titulo_carrera:         [''],
    descripcion_resolucion: [''],
    cod_codigo:             [''],
    lugar_x:                [''],
    carrera:                [''],
    area:                   [''],
    periodo:                [''],
    estado:                 [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Configuración registrada'); this.router.navigate(['/cenefco/configuracion-academica']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
