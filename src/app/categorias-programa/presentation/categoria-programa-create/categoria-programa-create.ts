import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CategoriaProgramaService } from '../../application/services/categoria-programa.service';
import { CategoriaProgramaItem } from '../../domain/models/categoria-programa.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { LUCIDE_ICONS } from '../../domain/lucide-icons';
import { generateSlug } from '../../../utils/slug';

@Component({
  selector: 'app-categoria-programa-create',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './categoria-programa-create.html',
  styles: ``
})
export class CategoriaProgramaCreate {
  private fb      = inject(FormBuilder);
  private service = inject(CategoriaProgramaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  submitting     = signal(false);
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

  constructor() {
    this.form.get('nombre')!.valueChanges.subscribe((nombre: string | null) => {
      this.form.get('slug')!.setValue(generateSlug(nombre ?? ''), { emitEvent: false });
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
    this.service.create(this.form.value as Partial<CategoriaProgramaItem>).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'Categoría de programa creada correctamente');
        this.router.navigate(['/cenefco/categorias-programa']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la categoría'));
        this.submitting.set(false);
      }
    });
  }
}
