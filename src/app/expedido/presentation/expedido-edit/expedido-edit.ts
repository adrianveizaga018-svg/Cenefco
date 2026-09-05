import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ExpedidoService } from '../../application/services/expedido.service';
import { Expedido } from '../../domain/models/expedido.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-expedido-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './expedido-edit.html',
  styles: ``
})
export class ExpedidoEdit implements OnInit {
  private fb      = inject(FormBuilder);
  private service = inject(ExpedidoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);

  submitting = signal(false);
  loading    = signal(true);
  private id!: number;

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(100)]],
    orden:  [0],
    activo: [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.form.patchValue({ nombre: item.nombre, orden: item.orden, activo: item.activo });
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el registro'));
        this.router.navigate(['/cenefco/expedido']);
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as Partial<Expedido>).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Registro actualizado correctamente');
        this.router.navigate(['/cenefco/expedido']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el registro'));
        this.submitting.set(false);
      }
    });
  }
}
