import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ConvenioService } from '../../application/services/convenio.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { ConvenioDetalle } from '../../domain/models/convenio.model';

@Component({ selector: 'app-convenio-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './convenio-edit.html' })
export class ConvenioEdit {
  private service = inject(ConvenioService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);

  submitting  = signal(false);
  loading     = signal(true);
  convenio    = signal<ConvenioDetalle | null>(null);
  id          = Number(this.route.snapshot.paramMap.get('id'));

  logoActual  = signal<string | null>(null);
  logoFile    = signal<File | null>(null);
  logoPreview = signal<string | null>(null);
  quitarLogoFlag = signal(false);

  docActual       = signal<string | null>(null);
  documentoFile   = signal<File | null>(null);
  quitarDocFlag   = signal(false);

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

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.convenio.set(d);
        this.logoActual.set(d.logo_url ?? null);
        this.docActual.set(d.documento_url ?? null);
        this.form.patchValue({ ...d as any, documento_url: null });
        this.loading.set(false);
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar el convenio'); this.router.navigate(['/cenefco/convenios']); }
    });
  }

  onLogoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.logoFile.set(file);
    this.quitarLogoFlag.set(false);
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
    this.quitarLogoFlag.set(true);
  }

  onDocumentoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.documentoFile.set(file);
    this.quitarDocFlag.set(false);
  }

  quitarDocumento(): void {
    this.documentoFile.set(null);
    this.quitarDocFlag.set(true);
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    const payload: any = {
      ...this.form.value,
      logo_file:      this.logoFile(),
      documento_file: this.documentoFile(),
    };
    if (this.quitarLogoFlag()) payload['quitar_logo'] = '1';
    if (this.quitarDocFlag())  payload['quitar_documento'] = '1';
    this.service.update(this.id, payload).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Convenio actualizado'); this.router.navigate(['/cenefco/convenios']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
