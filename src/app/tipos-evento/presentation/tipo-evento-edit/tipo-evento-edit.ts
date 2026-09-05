import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TipoEventoService } from '../../application/services/tipo-evento.service';
import { TipoEvento } from '../../domain/models/tipo-evento.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-tipo-evento-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './tipo-evento-edit.html',
  styles: ``
})
export class TipoEventoEdit implements OnInit {
  private fb                = inject(FormBuilder);
  private tipoEventoService  = inject(TipoEventoService);
  private toast             = inject(ToastService);
  private router            = inject(Router);
  private route             = inject(ActivatedRoute);

  submitting       = signal(false);
  loadingTipoEvento = signal(true);
  private id!: number;

  form = this.fb.group({ nombre: ['', [Validators.required, Validators.maxLength(100)]] });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.tipoEventoService.getById(this.id).subscribe({
      next: (t) => { this.form.patchValue({ nombre: t.nombre }); this.loadingTipoEvento.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el tipo de evento')); this.router.navigate(['/cenefco/tipos-evento']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.tipoEventoService.update(this.id, this.form.value as unknown as Partial<TipoEvento>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El tipo de evento ha sido actualizado correctamente'); this.router.navigate(['/cenefco/tipos-evento']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el tipo de evento')); this.submitting.set(false); }
    });
  }
}
