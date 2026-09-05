import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { VendedorService } from '../../application/services/vendedor.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { extractErrorMessage } from '../../../utils/http-error';

interface UsuarioOpt { id: number; nombre: string; apellido: string; }

@Component({
  selector: 'app-vendedor-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './vendedor-edit.html',
})
export class VendedorEdit implements OnInit {
  private service     = inject(VendedorService);
  private toast       = inject(ToastService);
  private router      = inject(Router);
  private route       = inject(ActivatedRoute);
  private fb          = inject(FormBuilder);
  private http        = inject(HttpClient);
  private fileUpload  = inject(FileUploadService);

  id = Number(this.route.snapshot.paramMap.get('id'));

  submitting      = signal(false);
  loading         = signal(true);
  uploadingFoto   = signal(false);
  fotoPreview     = signal<string | null>(null);
  usuarios        = signal<UsuarioOpt[]>([]);
  usuariosLoading = signal(true);

  form: FormGroup = this.fb.group({
    nombre:      ['', [Validators.required, Validators.maxLength(150)]],
    apellido:    ['', [Validators.required, Validators.maxLength(150)]],
    ci:          [null as string | null, [Validators.maxLength(20)]],
    telefono:    [null as string | null, [Validators.maxLength(30)]],
    email:       [null as string | null, [Validators.email, Validators.maxLength(255)]],
    foto:        [null as string | null],
    pagina:      [null as string | null, [Validators.maxLength(150)]],
    meta_ventas: [null as number | null, [Validators.min(0)]],
    activo:      [true],
    usuario_id:  [null as number | null],
  });

  ngOnInit(): void {
    this.http.get<{ data: UsuarioOpt[] }>('/api/v1/usuarios', { params: { pageSize: '200' } })
      .subscribe({ next: r => { this.usuarios.set(r.data); this.usuariosLoading.set(false); } });

    this.service.getById(this.id).subscribe({
      next: d => {
        this.form.patchValue({
          nombre:      d.nombre,
          apellido:    d.apellido,
          ci:          d.ci,
          telefono:    d.telefono,
          email:       d.email,
          foto:        d.foto,
          pagina:      d.pagina,
          meta_ventas: d.meta_ventas,
          activo:      d.activo,
          usuario_id:  d.usuario_id,
        });
        if (d.foto) this.fotoPreview.set(d.foto);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el vendedor');
        this.router.navigate(['/cenefco/vendedores']);
      },
    });
  }

  onFotoSelected(event: Event): void {
    this.fileUpload.handleImageSelect(event, {
      preview:     this.fotoPreview,
      uploading:   this.uploadingFoto,
      onSuccess:   (url) => this.form.patchValue({ foto: url }),
      fallbackMsg: 'No se pudo subir la foto',
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.getRawValue()).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Vendedor actualizado correctamente');
        this.router.navigate(['/cenefco/vendedores']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
        this.submitting.set(false);
      },
    });
  }
}
