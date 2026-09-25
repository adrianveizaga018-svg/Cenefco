import { Component, DestroyRef, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { ArticuloService } from '../../application/services/articulo.service';
import { EtiquetaService } from '../../../etiquetas/application/services/etiqueta.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { EtiquetaSimple } from '../../domain/models/articulo.model';
import { generateSlug } from '../../../utils/slug';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

@Component({
  selector: 'app-articulo-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle, CKEditorModule],
  templateUrl: './articulo-edit.html',
})
export class ArticuloEdit implements OnInit {
  private service         = inject(ArticuloService);
  private etiquetaService = inject(EtiquetaService);
  private toast           = inject(ToastService);
  private router          = inject(Router);
  private route           = inject(ActivatedRoute);
  private fb              = inject(FormBuilder);
  private fileUpload      = inject(FileUploadService);
  private cdr             = inject(ChangeDetectorRef);
  private destroyRef      = inject(DestroyRef);

  submitting             = signal(false);
  loading                = signal(true);
  uploading              = signal(false);
  imagePreview           = signal<string | null>(null);
  etiquetasDisponibles   = signal<EtiquetaSimple[]>([]);
  etiquetasSeleccionadas = signal<number[]>([]);
  private id!: number;
  private slug = this.route.snapshot.paramMap.get('slug') ?? '';
  private slugManual = true;

  Editor = ClassicEditor as any;
  private ckEditor: any = null;
  onEditorReady(editor: any): void { this.ckEditor = editor; }

  form = this.fb.group({
    titulo:               ['', [Validators.required, Validators.maxLength(200)]],
    slug:                 [''],
    entradilla:           ['', Validators.maxLength(500)],
    contenido:            [''],
    imagen_principal_url: [''],
    imagen_alt:           [''],
    destacada:            [false],
    fecha_publicacion:    [''],
    estado_web:           ['borrador'],
    meta_titulo:          [''],
    meta_descripcion:     [''],
  });

  ngOnInit(): void {
    this.etiquetaService.getAll({ pageSize: 200 })
      .subscribe({ next: r => this.etiquetasDisponibles.set(r.data as any) });

    this.form.get('titulo')!.valueChanges
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(titulo => {
        if (!this.slugManual) {
          this.form.get('slug')!.setValue(generateSlug(titulo ?? ''), { emitEvent: false });
        }
      });

    this.form.get('slug')!.valueChanges
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe(slug => {
        this.slugManual = !!slug;
      });

    this.service.getBySlug(this.slug).subscribe({
      next: (art) => {
        this.id = art.id_art;
        this.form.patchValue({
          titulo:               art.titulo,
          slug:                 art.slug ?? '',
          entradilla:           art.entradilla ?? '',
          contenido:            art.contenido ?? '',
          imagen_principal_url: art.imagen_principal_url ?? '',
          imagen_alt:           art.imagen_alt ?? '',
          destacada:            art.destacada,
          fecha_publicacion:    art.fecha_publicacion ? art.fecha_publicacion.slice(0, 16) : '',
          estado_web:           art.estado_web ?? 'borrador',
          meta_titulo:          art.meta_titulo ?? '',
          meta_descripcion:     art.meta_descripcion ?? '',
        });
        if (art.imagen_principal_url) {
          this.imagePreview.set(art.imagen_principal_url);
        }
        this.etiquetasSeleccionadas.set((art.etiquetas ?? []).map(e => e.id));
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el artículo');
        this.router.navigate(['/cenefco/articulos']);
      },
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
      this.toast.error('Archivo inválido', 'Solo se permiten imágenes.');
      return;
    }
    if (file.size > 20 * 1024 * 1024) {
      this.toast.error('Archivo muy grande', 'El tamaño máximo es 20 MB.');
      return;
    }

    this.fileUpload.handleImageSelect(event, {
      preview:     this.imagePreview,
      uploading:   this.uploading,
      onSuccess:   (url) => {
        this.form.get('imagen_principal_url')!.setValue(url);
        this.toast.success('Imagen subida', 'La imagen fue cargada correctamente.');
      },
      fallbackMsg: 'No se pudo subir la imagen.',
    });
    input.value = '';
  }

  removeImage(): void {
    this.imagePreview.set(null);
    this.form.get('imagen_principal_url')!.setValue('');
  }

  toggleEtiqueta(id: number): void {
    this.etiquetasSeleccionadas.update(ids =>
      ids.includes(id) ? ids.filter(i => i !== id) : [...ids, id]
    );
  }

  tieneEtiqueta(id: number): boolean {
    return this.etiquetasSeleccionadas().includes(id);
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.ckEditor) this.form.get('contenido')?.setValue(this.ckEditor.getData());
    this.submitting.set(true);
    const payload = { ...this.form.value, etiquetas: this.etiquetasSeleccionadas() };
    this.service.update(this.id, payload as any).subscribe({
      next: () => {
        this.toast.success('¡Actualizado!', 'Artículo actualizado correctamente');
        this.router.navigate(['/cenefco/articulos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
        this.submitting.set(false);
      },
    });
  }
}
