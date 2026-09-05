import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ComunicadoService } from '../../application/services/comunicado.service';
import { Comunicado } from '../../domain/models/comunicado.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-comunicado-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './comunicado-edit.html',
  styles: ``
})
export class ComunicadoEdit implements OnInit {
  private fb                = inject(FormBuilder);
  private comunicadoService = inject(ComunicadoService);
  private toast             = inject(ToastService);
  private router            = inject(Router);
  private route             = inject(ActivatedRoute);

  submitting        = signal(false);
  loadingComunicado = signal(true);
  private id!: number;
  private slug!: string;

  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    estado: ['borrador', [Validators.required]],
  });

  ngOnInit(): void {
    this.slug = this.route.snapshot.paramMap.get('slug') ?? '';
    this.comunicadoService.getBySlug(this.slug).subscribe({
      next: (c) => { this.id = c.id; this.form.patchValue({ titulo: c.titulo, estado: c.estado }); this.loadingComunicado.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el comunicado')); this.router.navigate(['/cenefco/comunicados']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.comunicadoService.update(this.id, this.form.value as unknown as Partial<Comunicado>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El comunicado ha sido actualizado correctamente'); this.router.navigate(['/cenefco/comunicados']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el comunicado')); this.submitting.set(false); }
    });
  }
}
