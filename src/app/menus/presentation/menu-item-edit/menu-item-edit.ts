import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { MenuItemService } from '../../application/services/menu-item.service';
import { MenuItem } from '../../domain/models/menu-item.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-menu-item-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './menu-item-edit.html',
  styles: ``
})
export class MenuItemEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(MenuItemService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting = signal(false);
  loading    = signal(true);
  siblings   = signal<MenuItem[]>([]);
  menuId     = 0;
  private id!: number;

  form = this.fb.group({
    etiqueta:            ['', [Validators.required, Validators.maxLength(120)]],
    url:                 ['' as string | null],
    parent_id:           [null as number | null],
    orden:               [0],
    icono:               ['' as string | null],
    activo:              [true],
    abrir_nueva_ventana: [false],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.menuId = item.menu_id;
        this.form.patchValue({
          etiqueta:            item.etiqueta,
          url:                 item.url ?? '',
          parent_id:           item.parent_id,
          orden:               item.orden,
          icono:               item.icono ?? '',
          activo:              item.activo,
          abrir_nueva_ventana: item.abrir_nueva_ventana,
        });
        this.service.getAll({ menu_id: item.menu_id, pageSize: 200 }).subscribe({
          next: r => this.siblings.set(r.data.filter(s => s.id !== this.id)),
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el ítem'));
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
      menu_id:             this.menuId,
      etiqueta:            value.etiqueta!,
      url:                 value.url || null,
      parent_id:           value.parent_id ?? null,
      orden:               value.orden ?? 0,
      icono:               value.icono || null,
      activo:              value.activo ?? true,
      abrir_nueva_ventana: value.abrir_nueva_ventana ?? false,
    }).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Ítem actualizado correctamente');
        this.router.navigate(['/cenefco/menu-items', this.menuId]);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el ítem'));
        this.submitting.set(false);
      }
    });
  }
}
