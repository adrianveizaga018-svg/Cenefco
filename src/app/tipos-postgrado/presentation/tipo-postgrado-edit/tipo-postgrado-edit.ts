import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { TipoPostgradoService } from '../../application/services/tipo-postgrado.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-postgrado-edit',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './tipo-postgrado-edit.html',
})
export class TipoPostgradoEdit {
  private service = inject(TipoPostgradoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private cdr     = inject(ChangeDetectorRef);
  submitting = signal(false);
  loading    = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    id_tipopago:        [null as number | null],
    descuentopostgrado: [''],
    calculo_cuota:      [''],
    estado:             [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/tipos-postgrado']); },
    });
  }

  onSubmit(): void {
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Tipo de postgrado actualizado'); this.router.navigate(['/cenefco/tipos-postgrado']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); },
    });
  }
}
