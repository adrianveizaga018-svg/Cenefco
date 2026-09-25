import { Component, computed, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { ResenaService } from '../../application/services/resena.service';
import { FileUploadService } from '../../../common/application/services/file-upload.service';
import { CursoService } from '../../../cursos/application/services/curso.service';
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
  private service    = inject(ResenaService);
  private toast      = inject(ToastService);
  private router     = inject(Router);
  private fb         = inject(FormBuilder);
  private fileUpload = inject(FileUploadService);
  private cursoSvc   = inject(CursoService);

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
    this.cursoSvc.getAll({ pageSize: 200 })
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
    this.fileUpload.handleImageSelect(event, {
      preview:     { set: (_: string | null) => {} } as any,
      uploading:   this.uploadingFoto,
      onSuccess:   (url) => this.form.patchValue({ foto_url: url }),
      fallbackMsg: 'No se pudo subir la foto',
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
