import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CifraInstitucionalService } from '../../application/services/cifra-institucional.service';
import { CifraInstitucional } from '../../domain/models/cifra-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-cifra-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './cifra-edit.html',
  styles: ``
})
export class CifraEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(CifraInstitucionalService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting = signal(false);
  loading    = signal(true);
  private id!: number;

  form = this.fb.group({
    valor:       ['', [Validators.required]],
    etiqueta:    ['', [Validators.required]],
    descripcion: [''],
    icono:       [''],
    color:       [''],
    orden:       [0],
    activo:      [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.form.patchValue({
          valor:       item.valor,
          etiqueta:    item.etiqueta,
          descripcion: item.descripcion ?? '',
          icono:       item.icono ?? '',
          color:       item.color ?? '',
          orden:       item.orden,
          activo:      item.activo,
        });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la cifra'));
        this.router.navigate(['/cenefco/cifras-institucionales']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as Partial<CifraInstitucional>).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'Cifra actualizada correctamente');
        this.router.navigate(['/cenefco/cifras-institucionales']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la cifra'));
        this.submitting.set(false);
      }
    });
  }
}
