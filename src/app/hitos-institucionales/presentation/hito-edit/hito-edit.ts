import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { HitoInstitucionalService } from '../../application/services/hito-institucional.service';
import { HitoInstitucional } from '../../domain/models/hito-institucional.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FileUploadService } from '../../../common/application/services/file-upload.service';

@Component({
  selector: 'app-hito-edit',
  imports: [NgIcon, PageTitle, RouterLink, ReactiveFormsModule],
  templateUrl: './hito-edit.html',
  styles: ``
})
export class HitoEdit implements OnInit {
  private fb          = inject(FormBuilder);
  private service     = inject(HitoInstitucionalService);
  private toast       = inject(ToastService);
  private router      = inject(Router);
  private route       = inject(ActivatedRoute);
  private fileUpload  = inject(FileUploadService);

  submitting    = signal(false);
  loading       = signal(true);
  uploadingImg  = signal(false);
  imgPreview    = signal<string | null>(null);
  private id!: number;

  form = this.fb.group({
    anio:        ['', [Validators.required]],
    titulo:      ['', [Validators.required]],
    descripcion: [''],
    imagen_url:  [''],
    imagen_alt:  [''],
    orden:       [0],
    activo:      [true],
  });

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: (item) => {
        this.form.patchValue({
          anio:        item.anio,
          titulo:      item.titulo,
          descripcion: item.descripcion ?? '',
          imagen_url:  item.imagen_url ?? '',
          imagen_alt:  item.imagen_alt ?? '',
          orden:       item.orden,
          activo:      item.activo,
        });
        if (item.imagen_url) this.imgPreview.set(item.imagen_url);
        this.loading.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo cargar el hito'));
        this.router.navigate(['/cenefco/hitos-institucionales']);
      }
    });
  }

  onImgSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.imgPreview,
      uploading:   this.uploadingImg,
      onSuccess:   (url) => this.form.patchValue({ imagen_url: url }),
      fallbackMsg: 'No se pudo subir la imagen',
    });
  }

  removeImg(): void {
    this.imgPreview.set(null);
    this.form.patchValue({ imagen_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    if (this.uploadingImg()) return;
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as Partial<HitoInstitucional>).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Hito actualizado correctamente');
        this.router.navigate(['/cenefco/hitos-institucionales']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el hito'));
        this.submitting.set(false);
      }
    });
  }
}
