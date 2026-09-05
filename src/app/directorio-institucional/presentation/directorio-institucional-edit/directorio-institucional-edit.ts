import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DirectorioInstitucionalService } from '../../application/services/directorio-institucional.service';
import { DirectorioInstitucional } from '../../domain/models/directorio-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-directorio-institucional-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './directorio-institucional-edit.html',
  styles: ``
})
export class DirectorioInstitucionalEdit implements OnInit {
  private fb                              = inject(FormBuilder);
  private directorioInstitucionalService   = inject(DirectorioInstitucionalService);
  private toast                           = inject(ToastService);
  private router                          = inject(Router);
  private route                           = inject(ActivatedRoute);

  submitting                     = signal(false);
  loadingDirectorioInstitucional  = signal(true);
  private id!: number;

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(150)]],
    cargo:  ['', [Validators.required, Validators.maxLength(150)]],
    orden:  [0, [Validators.required]],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.directorioInstitucionalService.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue({ nombre: d.nombre, cargo: d.cargo, orden: d.orden }); this.loadingDirectorioInstitucional.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la entrada')); this.router.navigate(['/cenefco/directorio-institucional']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.directorioInstitucionalService.update(this.id, this.form.value as unknown as Partial<DirectorioInstitucional>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'La entrada ha sido actualizada correctamente'); this.router.navigate(['/cenefco/directorio-institucional']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la entrada')); this.submitting.set(false); }
    });
  }
}
