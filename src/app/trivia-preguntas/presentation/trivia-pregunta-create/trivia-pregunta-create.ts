import { ChangeDetectorRef, Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaPreguntaService } from '../../application/services/trivia-pregunta.service';
import { TriviaOpcion } from '../../domain/models/trivia-pregunta.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { TriviaCategoriaService } from '../../../trivia-categorias/application/services/trivia-categoria.service';
import { TriviaCategoria } from '../../../trivia-categorias/domain/models/trivia-categoria.model';
import { TriviaNivelService } from '../../../trivia-niveles/application/services/trivia-nivel.service';
import { TriviaNivel } from '../../../trivia-niveles/domain/models/trivia-nivel.model';

@Component({
  selector: 'app-trivia-pregunta-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './trivia-pregunta-create.html',
  styles: ``
})
export class TriviaPreguntaCreate implements OnInit {
  private fb              = inject(FormBuilder);
  private service         = inject(TriviaPreguntaService);
  private categoriaService = inject(TriviaCategoriaService);
  private nivelService     = inject(TriviaNivelService);
  private toast           = inject(ToastService);
  private router          = inject(Router);
  private http            = inject(HttpClient);
  private cdr             = inject(ChangeDetectorRef);

  submitting   = signal(false);
  uploadingImg = signal(false);
  imgPreview   = signal<string | null>(null);
  categorias   = signal<TriviaCategoria[]>([]);
  niveles      = signal<TriviaNivel[]>([]);

  opciones = signal<TriviaOpcion[]>([
    { texto: '', es_correcta: true },
    { texto: '', es_correcta: false },
  ]);

  form: FormGroup = this.fb.group({
    categoria_id:            [null as number | null, [Validators.required]],
    nivel_id:                [null as number | null, [Validators.required]],
    enunciado:               ['', [Validators.required]],
    imagen_url:               [''],
    tiempo_limite_segundos:   [20, [Validators.required, Validators.min(5), Validators.max(120)]],
    activo:                  [true],
  });

  ngOnInit(): void {
    this.categoriaService.getAll({ pageSize: 200 }).subscribe({ next: (res) => this.categorias.set(res.data) });

    this.form.get('categoria_id')!.valueChanges.subscribe((categoriaId) => {
      this.form.patchValue({ nivel_id: null }, { emitEvent: false });
      this.niveles.set([]);
      if (!categoriaId) return;
      this.nivelService.getByCategoria(Number(categoriaId)).subscribe({ next: (niveles) => this.niveles.set(niveles) });
    });
  }

  agregarOpcion(): void {
    if (this.opciones().length >= 6) return;
    this.opciones.update(list => [...list, { texto: '', es_correcta: false }]);
  }

  quitarOpcion(i: number): void {
    if (this.opciones().length <= 2) return;
    const eraCorrecta = this.opciones()[i].es_correcta;
    this.opciones.update(list => {
      const arr = list.filter((_, idx) => idx !== i);
      if (eraCorrecta && arr.length) arr[0] = { ...arr[0], es_correcta: true };
      return arr;
    });
  }

  actualizarTexto(i: number, texto: string): void {
    this.opciones.update(list => {
      const arr = [...list];
      arr[i] = { ...arr[i], texto };
      return arr;
    });
  }

  marcarCorrecta(i: number): void {
    this.opciones.update(list => list.map((op, idx) => ({ ...op, es_correcta: idx === i })));
  }

  onImagenSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => { this.imgPreview.set(e.target?.result as string); this.cdr.detectChanges(); };
    reader.readAsDataURL(file);

    this.uploadingImg.set(true);
    const formData = new FormData();
    formData.append('file', file);

    this.http.post<{ url: string }>('/api/v1/upload/image', formData).subscribe({
      next: (res) => {
        this.form.patchValue({ imagen_url: res.url });
        this.imgPreview.set(res.url);
        this.uploadingImg.set(false);
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo subir la imagen'));
        this.imgPreview.set(null);
        this.uploadingImg.set(false);
        input.value = '';
        this.cdr.detectChanges();
      }
    });
  }

  removeImagen(): void {
    this.imgPreview.set(null);
    this.form.patchValue({ imagen_url: '' });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.uploadingImg()) return;

    const opciones = this.opciones();
    if (opciones.some(o => !o.texto.trim())) {
      this.toast.error('Error', 'Todas las opciones deben tener texto.');
      return;
    }
    if (opciones.filter(o => o.es_correcta).length !== 1) {
      this.toast.error('Error', 'Debe marcarse exactamente una opción como correcta.');
      return;
    }

    this.submitting.set(true);
    const val = this.form.value;
    this.service.create({
      ...val,
      tiempo_limite_segundos: Number(val.tiempo_limite_segundos) || 20,
      opciones: opciones.map((o, i) => ({ texto: o.texto.trim(), es_correcta: o.es_correcta, orden: i })),
    }).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'La pregunta ha sido creada correctamente');
        this.router.navigate(['/cenefco/trivia-preguntas']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo crear la pregunta'));
        this.submitting.set(false);
      }
    });
  }
}
