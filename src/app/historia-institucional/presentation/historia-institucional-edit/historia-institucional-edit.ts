import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { HistoriaInstitucionalService } from '../../application/services/historia-institucional.service';
import { HistoriaInstitucional } from '../../domain/models/historia-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-historia-institucional-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './historia-institucional-edit.html',
  styles: ``
})
export class HistoriaInstitucionalEdit implements OnInit {
  private fb                            = inject(FormBuilder);
  private historiaInstitucionalService   = inject(HistoriaInstitucionalService);
  private toast                         = inject(ToastService);
  private router                        = inject(Router);
  private route                         = inject(ActivatedRoute);

  submitting                   = signal(false);
  loadingHistoriaInstitucional  = signal(true);
  private id!: number;

  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.historiaInstitucionalService.getById(this.id).subscribe({
      next: (h) => { this.form.patchValue({ titulo: h.titulo, orden: h.orden, activo: h.activo }); this.loadingHistoriaInstitucional.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la entrada')); this.router.navigate(['/cenefco/historia-institucional']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.historiaInstitucionalService.update(this.id, this.form.value as unknown as Partial<HistoriaInstitucional>).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'La entrada ha sido actualizada correctamente'); this.router.navigate(['/cenefco/historia-institucional']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la entrada')); this.submitting.set(false); }
    });
  }
}
