import { Component, computed, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CategoriaProgramaService } from '../../application/services/categoria-programa.service';
import { CategoriaProgramaItem } from '../../domain/models/categoria-programa.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { LUCIDE_ICONS } from '../../domain/lucide-icons';
import { generateSlug } from '../../../utils/slug';

@Component({
  selector: 'app-categoria-programa-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './categoria-programa-edit.html',
  styles: ``
})
export class CategoriaProgramaEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(CategoriaProgramaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting   = signal(false);
  loading      = signal(true);
  categoriaId  = signal(0);

  pickerOpen     = signal(false);
  iconSearch     = signal('');
  readonly allIcons = LUCIDE_ICONS;
  iconosVisibles = computed(() => {
    const q = this.iconSearch().toLowerCase();
    return q ? this.allIcons.filter((i: string) => i.toLowerCase().includes(q)) : this.allIcons;
  });

  form = this.fb.group({
    nombre:           ['', [Validators.required, Validators.maxLength(200)]],
    slug:             [''],
    descripcion:      [''],
    icono:            [''],
    color:            [''],
    orden:            [0],
    activo:           [true],
    meta_titulo:      [''],
    meta_descripcion: [''],
    tipo_programa_id: [null as number | null],
    comision_monto:   [0, [Validators.required, Validators.min(0.01)]],
  });

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.categoriaId.set(id);

    this.form.get('nombre')!.valueChanges.subscribe((nombre: string | null) => {
      this.form.get('slug')!.setValue(generateSlug(nombre ?? ''), { emitEvent: false });
    });

    this.service.getById(id).subscribe({
      next: (item) => {
        this.form.patchValue({
          nombre:           item.nombre,
          slug:             item.slug,
          descripcion:      item.descripcion ?? '',
          icono:            item.icono ?? '',
          color:            item.color ?? '',
          orden:            item.orden,
          activo:           item.activo,
          meta_titulo:      item.meta_titulo ?? '',
          meta_descripcion: item.meta_descripcion ?? '',
          tipo_programa_id: item.tipo_programa_id ?? null,
          comision_monto:   item.comision_monto,
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la categoría'));
        this.router.navigate(['/cenefco/categorias-programa']);
      }
    });
  }

  selectIcon(name: string): void {
    this.form.get('icono')!.setValue(name);
    this.pickerOpen.set(false);
    this.iconSearch.set('');
  }

  clearIcon(): void {
    this.form.get('icono')!.setValue('');
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.update(this.categoriaId(), this.form.value as Partial<CategoriaProgramaItem>).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'Categoría actualizada correctamente');
        this.router.navigate(['/cenefco/categorias-programa']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la categoría'));
        this.submitting.set(false);
      }
    });
  }
}
