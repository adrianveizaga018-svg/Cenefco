import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ConvenioService } from '../../application/services/convenio.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({ selector: 'app-convenio-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './convenio-create.html' })
export class ConvenioCreate {
  private service = inject(ConvenioService); private toast = inject(ToastService);
  private router  = inject(Router); private fb = inject(FormBuilder);
  submitting = signal(false);

  logoFile      = signal<File | null>(null);
  logoPreview   = signal<string | null>(null);
  documentoFile = signal<File | null>(null);

  form = this.fb.group({
    nombre:             ['', [Validators.required, Validators.maxLength(300)]],
    institucion:        [''],
    tipo:               [''],
    descripcion:        [''],
    responsable:        [''],
    contacto_email:     [''],
    contacto_telefono:  [''],
    fecha_inicio:       [''],
    fecha_fin:          [''],
    estado:             ['activo'],
    orden:              [0],
  });

  onLogoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.logoFile.set(file);
    if (file) {
      const reader = new FileReader();
      reader.onload = e => this.logoPreview.set(e.target?.result as string);
      reader.readAsDataURL(file);
    } else {
      this.logoPreview.set(null);
    }
  }

  quitarLogo(): void {
    this.logoFile.set(null);
    this.logoPreview.set(null);
  }

  onDocumentoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.documentoFile.set(file);
  }

  quitarDocumento(): void {
    this.documentoFile.set(null);
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create({ ...this.form.value as any, logo_file: this.logoFile(), documento_file: this.documentoFile() }).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Convenio registrado'); this.router.navigate(['/cenefco/convenios']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
