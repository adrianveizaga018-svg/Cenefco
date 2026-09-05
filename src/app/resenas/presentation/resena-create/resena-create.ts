import { Component, computed, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { ResenaService } from '../../application/services/resena.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { EstudianteResena } from '../../domain/models/resena.model';

interface ProgramaOpt { id_programa: number; nombre_programa: string; }

@Component({
  selector: 'app-resena-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './resena-create.html',
})
export class ResenaCreate implements OnInit {
  private service = inject(ResenaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);

  submitting         = signal(false);
  programas          = signal<ProgramaOpt[]>([]);
  programasLoading   = signal(true);
  estudiantes        = signal<EstudianteResena[]>([]);
  estudiantesLoading = signal(false);
  uploadingFoto      = signal(false);
  calificacionHover  = signal(0);
  calificacionValor  = signal(5);

  private readonly CALIFICACION_LABELS: Record<number, string> = {
    5: 'Excelente', 4: 'Muy bueno', 3: 'Bueno', 2: 'Regular', 1: 'Malo',
  };

  calificacionLabel = computed(() => {
    const val = this.calificacionHover() || this.calificacionValor();
    return this.CALIFICACION_LABELS[val] ?? '';
  });

  setCalificacion(valor: number): void {
    this.calificacionValor.set(valor);
    this.form.patchValue({ calificacion: valor });
  }

  form = this.fb.group({
    programa_id:   [null as number | null, Validators.required],
    usuario_id:    [null as number | null],
    nombre:        ['', [Validators.required, Validators.maxLength(200)]],
    cargo_actual:  [''],
    foto_url:      [''],
    calificacion:  [5, [Validators.required, Validators.min(1), Validators.max(5)]],
    titulo_resena: [''],
    resena:        ['', Validators.required],
    estado:        ['aprobada'],
    verificado:    [true],
    destacada:     [false],
  });

  ngOnInit(): void {
    this.http.get<{ data: ProgramaOpt[] }>('/api/v1/cursos', { params: { pageSize: '200' } })
      .subscribe({ next: r => { this.programas.set(r.data); this.programasLoading.set(false); } });
  }

  onProgramaChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    if (!id) { this.estudiantes.set([]); return; }
    this.form.patchValue({ usuario_id: null, nombre: '' });
    this.estudiantesLoading.set(true);
    this.service.getEstudiantesPrograma(id).subscribe({
      next: r => { this.estudiantes.set(r.data); this.estudiantesLoading.set(false); },
      error: () => this.estudiantesLoading.set(false),
    });
  }

  onEstudianteChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    if (!id) return;
    const est = this.estudiantes().find(e => e.id_us === id);
    if (est) {
      this.form.patchValue({ nombre: est.nombre_completo, usuario_id: est.id_us });
    }
  }

  estrellas(): number[] { return [1, 2, 3, 4, 5]; }

  onFotoSeleccionada(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    this.uploadingFoto.set(true);
    const fd = new FormData();
    fd.append('file', file);
    this.http.post<{ url: string }>('/api/v1/upload/image', fd).subscribe({
      next: (res) => { this.form.patchValue({ foto_url: res.url }); this.uploadingFoto.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo subir la foto'); this.uploadingFoto.set(false); input.value = ''; },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'Reseña registrada correctamente');
        this.router.navigate(['/cenefco/resenas']);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }
}
