import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { MoodleService } from '../../application/services/moodle.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-moodle-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './moodle-create.html',
})
export class MoodleCreate {
  private service = inject(MoodleService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_moodle:             [this.autoId, [Validators.required]],
    titulo_moodle:         ['', [Validators.required]],
    cp_moodle_servidor:    [null as string | null],
    cp_moodle_base_datos:  [null as string | null],
    cp_moodle_usuario_bd:  [null as string | null],
    cp_moodle_contrasena:  [null as string | null],
    cp_url_campus:         [null as string | null],
    estado:                [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Moodle registrado');
        this.router.navigate(['/cenefco/moodles']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }
}
