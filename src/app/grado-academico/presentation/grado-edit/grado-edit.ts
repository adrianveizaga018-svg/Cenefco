import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { GradoAcademicoService } from '../../application/services/grado-academico.service';
import { GradoAcademico } from '../../domain/models/grado-academico.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-grado-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './grado-edit.html',
  styles: ``
})
export class GradoEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(GradoAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting = signal(false);
  loading    = signal(true);
  private id!: number;

  form = this.fb.group({
    nombre:          ['', [Validators.required, Validators.maxLength(100)]],
    abreviatura:     ['', [Validators.required, Validators.maxLength(20)]],
    requiere_titulo: [false],
    orden:           [0],
    activo:          [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.form.patchValue({
          nombre:          item.nombre,
          abreviatura:     item.abreviatura,
          requiere_titulo: item.requiere_titulo,
          orden:           item.orden,
          activo:          item.activo,
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el grado académico'));
        this.router.navigate(['/cenefco/grados-academicos']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as Partial<GradoAcademico>).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Grado académico actualizado correctamente');
        this.router.navigate(['/cenefco/grados-academicos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el grado académico'));
        this.submitting.set(false);
      }
    });
  }
}
