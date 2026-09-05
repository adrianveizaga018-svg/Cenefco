import { Component, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { MenuService } from '../../application/services/menu.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-menu-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './menu-create.html',
  styles: ``
})
export class MenuCreate {
  private fb      = inject(FormBuilder);
  private service = inject(MenuService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  submitting = signal(false);

  form = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(80)]],
    descripcion: ['' as string | null],
    activo:      [true],
  });

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    const value = this.form.value;
    this.service.create({
      nombre:      value.nombre!,
      descripcion: value.descripcion || null,
      activo:      value.activo ?? true,
    }).subscribe({
      next: () => {
        this.toast.success('¡Creado!', 'Menú creado correctamente');
        this.router.navigate(['/cenefco/menus']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el menú'));
        this.submitting.set(false);
      }
    });
  }
}
