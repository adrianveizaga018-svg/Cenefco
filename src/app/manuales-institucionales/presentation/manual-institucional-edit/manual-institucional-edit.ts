import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ManualInstitucionalService } from '../../application/services/manual-institucional.service';
import { ManualInstitucional } from '../../domain/models/manual-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-manual-institucional-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './manual-institucional-edit.html',
  styles: ``
})
export class ManualInstitucionalEdit implements OnInit {
  private fb                         = inject(FormBuilder);
  private manualInstitucionalService  = inject(ManualInstitucionalService);
  private toast                      = inject(ToastService);
  private router                     = inject(Router);
  private route                      = inject(ActivatedRoute);

  submitting                = signal(false);
  loadingManualInstitucional = signal(true);
  private id!: number;

  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.manualInstitucionalService.getById(this.id).subscribe({
      next: (m) => { this.form.patchValue({ titulo: m.titulo, orden: m.orden, activo: m.activo }); this.loadingManualInstitucional.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el manual')); this.router.navigate(['/cenefco/manuales-institucionales']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.manualInstitucionalService.update(this.id, this.form.value as unknown as Partial<ManualInstitucional>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El manual ha sido actualizado correctamente'); this.router.navigate(['/cenefco/manuales-institucionales']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el manual')); this.submitting.set(false); }
    });
  }
}
