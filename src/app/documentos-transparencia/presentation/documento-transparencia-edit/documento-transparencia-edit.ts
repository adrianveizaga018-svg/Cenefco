import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { DocumentoTransparenciaService } from '../../application/services/documento-transparencia.service';
import { DocumentoTransparencia } from '../../domain/models/documento-transparencia.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-documento-transparencia-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './documento-transparencia-edit.html',
  styles: ``
})
export class DocumentoTransparenciaEdit implements OnInit {
  private fb                             = inject(FormBuilder);
  private documentoTransparenciaService   = inject(DocumentoTransparenciaService);
  private toast                          = inject(ToastService);
  private router                         = inject(Router);
  private route                          = inject(ActivatedRoute);

  submitting                   = signal(false);
  loadingDocumentoTransparencia = signal(true);
  private id!: number;

  form = this.fb.group({
    titulo: ['', [Validators.required, Validators.maxLength(255)]],
    estado: ['activo', [Validators.required]],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.documentoTransparenciaService.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue({ titulo: d.titulo, estado: d.estado }); this.loadingDocumentoTransparencia.set(false); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el documento')); this.router.navigate(['/cenefco/documentos-transparencia']); }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.documentoTransparenciaService.update(this.id, this.form.value as unknown as Partial<DocumentoTransparencia>).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'El documento ha sido actualizado correctamente'); this.router.navigate(['/cenefco/documentos-transparencia']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el documento')); this.submitting.set(false); }
    });
  }
}
