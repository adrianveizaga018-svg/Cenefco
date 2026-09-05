import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SecretariaService } from '../../application/services/secretaria.service';
import { Secretaria } from '../../domain/models/secretaria.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-secretaria-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './secretaria-edit.html',
  styles: ``
})
export class SecretariaEdit implements OnInit {
  private fb                = inject(FormBuilder);
  private secretariaService = inject(SecretariaService);
  private toast             = inject(ToastService);
  private router            = inject(Router);
  private route             = inject(ActivatedRoute);

  submitting        = signal(false);
  loadingSecretaria = signal(true);
  private id!: number;
  private slug!: string;

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(150)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  ngOnInit(): void {
    this.slug = this.route.snapshot.paramMap.get('slug') ?? '';
    this.secretariaService.getBySlug(this.slug).subscribe({
      next: (s) => { this.id = s.id; this.form.patchValue({ nombre: s.nombre, orden: s.orden, activo: s.activo }); this.loadingSecretaria.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar la secretaría')); this.router.navigate(['/cenefco/secretarias']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.secretariaService.update(this.id, this.form.value as unknown as Partial<Secretaria>).subscribe({
      next: () => { this.toast.success('¡Actualizada!', 'La secretaría ha sido actualizada correctamente'); this.router.navigate(['/cenefco/secretarias']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la secretaría')); this.submitting.set(false); }
    });
  }
}
