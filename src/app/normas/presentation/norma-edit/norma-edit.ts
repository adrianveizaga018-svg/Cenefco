import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { NormaService } from '../../application/services/norma.service';
import { Norma } from '../../domain/models/norma.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-norma-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './norma-edit.html',
  styles: ``
})
export class NormaEdit implements OnInit {
  private fb           = inject(FormBuilder);
  private normaService = inject(NormaService);
  private toast        = inject(ToastService);
  private router       = inject(Router);
  private route        = inject(ActivatedRoute);

  submitting    = signal(false);
  loadingNorma  = signal(true);
  private id!: number;

  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    estado: ['vigente', [Validators.required]],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.normaService.getById(this.id).subscribe({
      next: (n) => { this.form.patchValue({ titulo: n.titulo, estado: n.estado }); this.loadingNorma.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la norma')); this.router.navigate(['/cenefco/normas']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.normaService.update(this.id, this.form.value as unknown as Partial<Norma>).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'La norma ha sido actualizada correctamente'); this.router.navigate(['/cenefco/normas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la norma')); this.submitting.set(false); }
    });
  }
}
