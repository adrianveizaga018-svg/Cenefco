import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TipoNormaService } from '../../application/services/tipo-norma.service';
import { TipoNorma } from '../../domain/models/tipo-norma.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-norma-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './tipo-norma-edit.html',
  styles: ``
})
export class TipoNormaEdit implements OnInit {
  private fb               = inject(FormBuilder);
  private tipoNormaService = inject(TipoNormaService);
  private toast            = inject(ToastService);
  private router           = inject(Router);
  private route            = inject(ActivatedRoute);

  submitting      = signal(false);
  loadingTipoNorma = signal(true);
  private id!: number;

  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.tipoNormaService.getById(this.id).subscribe({
      next: (t) => { this.form.patchValue({ nombre: t.nombre }); this.loadingTipoNorma.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el tipo de norma')); this.router.navigate(['/cenefco/tipos-norma']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.tipoNormaService.update(this.id, this.form.value as unknown as Partial<TipoNorma>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El tipo de norma ha sido actualizado correctamente'); this.router.navigate(['/cenefco/tipos-norma']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el tipo de norma')); this.submitting.set(false); }
    });
  }
}
