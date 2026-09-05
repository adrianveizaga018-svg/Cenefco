import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { MdlCourseService } from '../../application/services/mdl-course.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-mdl-course-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './mdl-course-create.html',
})
export class MdlCourseCreate {
  private service = inject(MdlCourseService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id:                    [this.autoId, [Validators.required]],
    fullname:              [null as string | null],
    shortname:             [null as string | null],
    id_docente:            [null as number | null],
    sigla:                 [null as string | null],
    paralelo:              [null as string | null],
    cupo:                  [null as string | null],
    gestion:               [null as string | null],
    titulo_personalizado:  [null as string | null],
    imparte_fecha_inicio:  [null as string | null],
    imparte_fecha_fin:     [null as string | null],
    estado:                [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Curso registrado'); this.router.navigate(['/cenefco/mdl-courses']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
