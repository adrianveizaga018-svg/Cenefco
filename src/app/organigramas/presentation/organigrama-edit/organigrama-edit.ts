import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { OrganigramaService } from '../../application/services/organigrama.service';
import { Organigrama } from '../../domain/models/organigrama.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-organigrama-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './organigrama-edit.html',
  styles: ``
})
export class OrganigramaEdit implements OnInit {
  private fb                 = inject(FormBuilder);
  private organigramaService = inject(OrganigramaService);
  private toast              = inject(ToastService);
  private router             = inject(Router);
  private route              = inject(ActivatedRoute);

  submitting         = signal(false);
  loadingOrganigrama = signal(true);
  private id!: number;

  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    orden:  [0, [Validators.required]],
    activo: [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.organigramaService.getById(this.id).subscribe({
      next: (o) => { this.form.patchValue({ titulo: o.titulo, orden: o.orden, activo: o.activo }); this.loadingOrganigrama.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el organigrama')); this.router.navigate(['/cenefco/organigramas']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.organigramaService.update(this.id, this.form.value as unknown as Partial<Organigrama>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El organigrama ha sido actualizado correctamente'); this.router.navigate(['/cenefco/organigramas']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el organigrama')); this.submitting.set(false); }
    });
  }
}
