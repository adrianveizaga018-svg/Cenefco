import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { ProgramaAcademicoService } from '../../application/services/programa-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-programa-academico-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './programa-academico-create.html' })
export class ProgramaAcademicoCreate {
  private service = inject(ProgramaAcademicoService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_programa:              [this.autoId, [Validators.required]],
    nombre_programa:          ['', [Validators.required, Validators.maxLength(200)]],
    descripcion:              [''],
    dirigido:                 [''],
    inversion:                [''],
    requisitos:               [''],
    creditaje:                [''],
    objetivo:                 [''],
    nota:                     [''],
    id_tipoprograma:          [null as number | null],
    url_video:                [''],
    inicio_actividades:       [''],
    finalizacion_actividades: [''],
    inicio_inscripciones:     [''],
    estado:                   [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Programa académico registrado'); this.router.navigate(['/cenefco/programas-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
