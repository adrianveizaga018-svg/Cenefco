import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { ProgramaAcademicoService } from '../../application/services/programa-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-programa-academico-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './programa-academico-edit.html' })
export class ProgramaAcademicoEdit {
  private service = inject(ProgramaAcademicoService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    nombre_programa:          ['', [Validators.required, Validators.maxLength(200)]],
    descripcion:              [''], dirigido: [''], inversion: [''],
    requisitos:               [''], creditaje: [''], objetivo: [''], nota: [''],
    id_tipoprograma:          [null as number | null], url_video: [''],
    inicio_actividades:       [''], finalizacion_actividades: [''], inicio_inscripciones: [''],
    estado:                   [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/programas-academicos']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Programa actualizado'); this.router.navigate(['/cenefco/programas-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
