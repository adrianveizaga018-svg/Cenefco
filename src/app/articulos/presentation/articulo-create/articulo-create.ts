import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { ArticuloService } from '../../application/services/articulo.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { EtiquetaSimple } from '../../domain/models/articulo.model';
import { generateSlug } from '../../../utils/slug';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

@Component({
  selector: 'app-articulo-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle, CKEditorModule],
  templateUrl: './articulo-create.html',
})
export class ArticuloCreate implements OnInit {
  private service = inject(ArticuloService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  private cdr     = inject(ChangeDetectorRef);

  submitting             = signal(false);
  uploading              = signal(false);
  imagePreview           = signal<string | null>(null);
  etiquetasDisponibles   = signal<EtiquetaSimple[]>([]);
  etiquetasSeleccionadas = signal<number[]>([]);

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
    this.http.get<{ data: EtiquetaSimple[] }>('/api/v1/etiquetas', { params: { pageSize: '200' } })
      .subscribe({ next: r => this.etiquetasDisponibles.set(r.data) });

    this.form.get('titulo')!.valueChanges.subscribe(titulo => {
      this.form.get('slug')!.setValue(generateSlug(titulo ?? ''), { emitEvent: false });
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

    const reader = new FileReader();
    reader.onload = () => {
      this.imagePreview.set(reader.result as string);
      this.cdr.detectChanges();
    };
    reader.readAsDataURL(file);

    this.uploading.set(true);
    const formData = new FormData();
    formData.append('file', file);

    this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({
      next: res => {
        this.form.get('imagen_principal_url')!.setValue(res.url);
        this.uploading.set(false);
        this.toast.success('Imagen subida', 'La imagen fue cargada correctamente.');
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.uploading.set(false);
        this.imagePreview.set(null);
        this.toast.error('Error al subir', extractErrorMessage(err, 'No se pudo subir la imagen.'));
        this.cdr.detectChanges();
      },
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
    this.service.create(payload as any).subscribe({
      next: () => {
        this.toast.success('¡Publicado!', 'Artículo creado correctamente');
        this.router.navigate(['/cenefco/articulos']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }
}
