import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TipoDocumentoTransparenciaService } from '../../application/services/tipo-documento-transparencia.service';
import { TipoDocumentoTransparencia } from '../../domain/models/tipo-documento-transparencia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-documento-transparencia-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './tipo-documento-transparencia-edit.html',
  styles: ``
})
export class TipoDocumentoTransparenciaEdit implements OnInit {
  private fb                                   = inject(FormBuilder);
  private tipoDocumentoTransparenciaService     = inject(TipoDocumentoTransparenciaService);
  private toast                                = inject(ToastService);
  private router                               = inject(Router);
  private route                                = inject(ActivatedRoute);

  submitting                          = signal(false);
  loadingTipoDocumentoTransparencia    = signal(true);
  private id!: number;

  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.tipoDocumentoTransparenciaService.getById(this.id).subscribe({
      next: (t) => { this.form.patchValue({ nombre: t.nombre }); this.loadingTipoDocumentoTransparencia.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el tipo')); this.router.navigate(['/cenefco/tipos-documento-transparencia']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.tipoDocumentoTransparenciaService.update(this.id, this.form.value as unknown as Partial<TipoDocumentoTransparencia>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El tipo ha sido actualizado correctamente'); this.router.navigate(['/cenefco/tipos-documento-transparencia']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el tipo')); this.submitting.set(false); }
    });
  }
}
