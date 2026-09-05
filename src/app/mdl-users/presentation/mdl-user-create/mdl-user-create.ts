import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { MdlUserService } from '../../application/services/mdl-user.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-mdl-user-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './mdl-user-create.html',
})
export class MdlUserCreate {
  private service = inject(MdlUserService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id:             [this.autoId, [Validators.required]],
    nombre_usuario: [null as string | null],
    nombre:         [null as string | null],
    appaterno:      [null as string | null],
    apmaterno:      [null as string | null],
    ci:             [null as string | null],
    email:          [null as string | null],
    celular:        [null as string | null],
    ciudad:         [null as string | null],
    estado:         [1],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Usuario Moodle registrado'); this.router.navigate(['/cenefco/mdl-users']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); },
    });
  }
}
