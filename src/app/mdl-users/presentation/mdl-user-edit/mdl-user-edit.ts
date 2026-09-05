import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { MdlUserService } from '../../application/services/mdl-user.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-mdl-user-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './mdl-user-edit.html',
})
export class MdlUserEdit {
  private service = inject(MdlUserService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  submitting = signal(false);
  loading    = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
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

  constructor() {
    this.service.getById(this.id).subscribe({
      next: d => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/mdl-users']); },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Usuario actualizado'); this.router.navigate(['/cenefco/mdl-users']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); },
    });
  }
}
