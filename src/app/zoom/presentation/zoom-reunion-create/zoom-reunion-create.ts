import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { ZoomService } from '../../application/services/zoom.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { ZoomReunion } from '../../domain/models/zoom.model';

@Component({ selector: 'app-zoom-reunion-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './zoom-reunion-create.html' })
export class ZoomReunionCreate {
  private service = inject(ZoomService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);

  submitting    = signal(false);
  resultado     = signal<ZoomReunion[] | null>(null);

  form = this.fb.group({
    tipo:         ['unica', [Validators.required]],
    tema:         [''],
    curso:        [''],
    fecha_inicio: ['', [Validators.required]],
    duracion_min: [60, [Validators.min(15), Validators.max(480)]],
    n_sesiones:   [1, [Validators.min(1), Validators.max(52)]],
    dias_entre:   [7, [Validators.min(1)]],
  });

  get esMultisesion(): boolean { return this.form.get('tipo')?.value === 'multisesion'; }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);

    const v = this.form.value;
    const payload: any = {
      tipo:         v.tipo,
      fecha_inicio: v.fecha_inicio,
      duracion_min: v.duracion_min,
    };
    if (v.tipo === 'unica')       { payload.tema = v.tema; }
    if (v.tipo === 'multisesion') { payload.curso = v.curso; payload.n_sesiones = v.n_sesiones; payload.dias_entre = v.dias_entre; }

    this.service.crearReunion(payload).subscribe({
      next: (res) => {
        this.submitting.set(false);
        const sesiones = res.tipo === 'unica' ? [res.reunion!] : (res.sesiones ?? []);
        this.resultado.set(sesiones);
        this.toast.success('¡Creada!', res.tipo === 'unica' ? 'Reunión creada en Zoom' : `${sesiones.length} sesiones creadas en Zoom`);
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo crear la reunión en Zoom');
        this.submitting.set(false);
      }
    });
  }

  volver(): void { this.router.navigate(['/cenefco/zoom-reuniones']); }
}
