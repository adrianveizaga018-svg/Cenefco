import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { MdlCourseService } from '../../application/services/mdl-course.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-mdl-course-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './mdl-course-edit.html',
})
export class MdlCourseEdit {
  private service = inject(MdlCourseService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  loading    = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    fullname:             [null as string | null],
    shortname:            [null as string | null],
    id_docente:           [null as number | null],
    sigla:                [null as string | null],
    paralelo:             [null as string | null],
    cupo:                 [null as string | null],
    gestion:              [null as string | null],
    titulo_personalizado: [null as string | null],
    imparte_fecha_inicio: [null as string | null],
    imparte_fecha_fin:    [null as string | null],
    estado:               [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: d => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/mdl-courses']); },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Curso actualizado'); this.router.navigate(['/cenefco/mdl-courses']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); },
    });
  }
}
