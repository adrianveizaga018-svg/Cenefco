import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { WhatsappGrupoService } from '../../application/services/whatsapp-grupo.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-whatsapp-grupo-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './whatsapp-grupo-create.html' })
export class WhatsappGrupoCreate {
  private service = inject(WhatsappGrupoService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);

  form = this.fb.group({
    nombre:                  ['', [Validators.required, Validators.maxLength(200)]],
    enlace_invitacion:       ['', [Validators.required, Validators.maxLength(500)]],
    imparte_id:              [null as number | null, [Validators.required]],
    capacidad_maxima:        [null as number | null],
    miembros_actuales:       [0],
    descripcion:             [''],
    activo:                  [true],
    orden:                   [0],
    fecha_expiracion_enlace: [''],
  });

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Grupo de WhatsApp registrado'); this.router.navigate(['/cenefco/whatsapp-grupos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
