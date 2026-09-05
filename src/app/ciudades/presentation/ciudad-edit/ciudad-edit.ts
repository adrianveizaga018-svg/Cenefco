import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { CiudadService } from '../../application/services/ciudad.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-ciudad-edit',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './ciudad-edit.html',
})
export class CiudadEdit {
  private service = inject(CiudadService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);
  private cdr     = inject(ChangeDetectorRef);
  submitting = signal(false);
  loading    = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    nombre_ciudad: ['', [Validators.required, Validators.maxLength(200)]],
    estado:       [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/ciudades']); },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Ciudad actualizada'); this.router.navigate(['/cenefco/ciudades']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); },
    });
  }
}
