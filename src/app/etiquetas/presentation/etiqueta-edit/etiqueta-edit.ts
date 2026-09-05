import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EtiquetaService } from '../../application/services/etiqueta.service';
import { Etiqueta } from '../../domain/models/etiqueta.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-etiqueta-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './etiqueta-edit.html',
  styles: ``
})
export class EtiquetaEdit implements OnInit {
  private fb             = inject(FormBuilder);
  private etiquetaService = inject(EtiquetaService);
  private toast          = inject(ToastService);
  private router         = inject(Router);
  private route          = inject(ActivatedRoute);

  submitting      = signal(false);
  loadingEtiqueta = signal(true);
  private id!: number;
  private slug!: string;

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(100)]],
  });

  ngOnInit(): void {
    this.slug = this.route.snapshot.paramMap.get('slug') ?? '';
    this.etiquetaService.getBySlug(this.slug).subscribe({
      next: (etiqueta) => {
        this.id = etiqueta.id;
        this.form.patchValue({ nombre: etiqueta.nombre });
        this.loadingEtiqueta.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la etiqueta'));
        this.router.navigate(['/cenefco/etiquetas']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.etiquetaService.update(this.id, this.form.value as unknown as Partial<Etiqueta>).subscribe({
      next: () => {
        this.toast.success('¡Actualizada!', 'La etiqueta ha sido actualizada correctamente');
        this.router.navigate(['/cenefco/etiquetas']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la etiqueta'));
        this.submitting.set(false);
      }
    });
  }
}
