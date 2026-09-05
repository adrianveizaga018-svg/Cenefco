import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { WhatsappGrupoService } from '../../application/services/whatsapp-grupo.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-whatsapp-grupo-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './whatsapp-grupo-edit.html' })
export class WhatsappGrupoEdit {
  private service = inject(WhatsappGrupoService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

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

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/whatsapp-grupos']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Grupo actualizado'); this.router.navigate(['/cenefco/whatsapp-grupos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
