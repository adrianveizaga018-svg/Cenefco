import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SugerenciaReclamoService } from '../../application/services/sugerencia-reclamo.service';
import { SugerenciaReclamo } from '../../domain/models/sugerencia-reclamo.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-sugerencia-reclamo-detail',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './sugerencia-reclamo-detail.html',
  styles: ``
})
export class SugerenciaReclamoDetail implements OnInit {
  private fb                       = inject(FormBuilder);
  private sugerenciaReclamoService  = inject(SugerenciaReclamoService);
  private toast                    = inject(ToastService);
  private router                   = inject(Router);
  private route                    = inject(ActivatedRoute);

  submitting              = signal(false);
  loadingSugerenciaReclamo = signal(true);
  item                    = signal<SugerenciaReclamo | null>(null);
  private id!: number;

  form = this.fb.group({ respuesta: [''] });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.sugerenciaReclamoService.getById(this.id).subscribe({
      next: (s) => { this.item.set(s); this.form.patchValue({ respuesta: s.respuesta ?? '' }); this.loadingSugerenciaReclamo.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el registro')); this.router.navigate(['/cenefco/sugerencias-reclamos']); }
    });
  }

  onSubmit(): void {
    this.submitting.set(true);
    this.sugerenciaReclamoService.update(this.id, { respuesta: this.form.value.respuesta ?? '', estado: 'respondido' }).subscribe({
      next: () => { this.toast.success('¡Respondido!', 'La respuesta ha sido enviada correctamente'); this.router.navigate(['/cenefco/sugerencias-reclamos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar la respuesta')); this.submitting.set(false); }
    });
  }
}
