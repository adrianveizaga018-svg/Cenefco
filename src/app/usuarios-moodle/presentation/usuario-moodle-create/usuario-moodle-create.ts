import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { UsuarioMoodleService } from '../../application/services/usuario-moodle.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-usuario-moodle-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './usuario-moodle-create.html',
})
export class UsuarioMoodleCreate {
  private service = inject(UsuarioMoodleService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_usmoodle:    [this.autoId, [Validators.required]],
    id_us:          [null as number | null, [Validators.required]],
    id_moodle:      [null as number | null, [Validators.required]],
    moodle_id_user: [null as string | null],
    estado:         [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Registro creado'); this.router.navigate(['/cenefco/usuarios-moodle']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
