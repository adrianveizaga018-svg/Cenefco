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
  selector: 'app-menu-item-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './menu-item-create.html',
  styles: ``
})
export class MenuItemCreate implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(MenuItemService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  menuId     = 0;
  submitting = signal(false);
  siblings   = signal<MenuItem[]>([]);

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
    this.menuId = Number(this.route.snapshot.paramMap.get('menuId'));
    this.service.getAll({ menu_id: this.menuId, pageSize: 200 }).subscribe({
      next: r => this.siblings.set(r.data),
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    const value = this.form.value;
    this.service.create({
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
        this.toast.success('¡Creado!', 'Ítem creado correctamente');
        this.router.navigate(['/cenefco/menu-items', this.menuId]);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear el ítem'));
        this.submitting.set(false);
      }
    });
  }
}
