import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { MenuService } from '../../application/services/menu.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-menu-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './menu-edit.html',
  styles: ``
})
export class MenuEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(MenuService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting = signal(false);
  loading    = signal(true);
  private id!: number;

  form = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(80)]],
    descripcion: ['' as string | null],
    activo:      [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.form.patchValue({
          nombre:      item.nombre,
          descripcion: item.descripcion ?? '',
          activo:      item.activo,
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el menú'));
        this.router.navigate(['/cenefco/menus']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    const value = this.form.value;
    this.service.update(this.id, {
      nombre:      value.nombre!,
      descripcion: value.descripcion || null,
      activo:      value.activo ?? true,
    }).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Menú actualizado correctamente');
        this.router.navigate(['/cenefco/menus']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el menú'));
        this.submitting.set(false);
      }
    });
  }
}
