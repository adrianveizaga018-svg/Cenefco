import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, distinctUntilChanged, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle }  from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { CursoMigradoService } from '../../application/services/curso-migrado.service';
import { CursoMigrado, CursoMigradoListResponse, ParticipanteBusquedaListResponse } from '../../domain/models/curso-migrado.model';
import { ToastService } from '../../../common/application/services/toast.service';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: CursoMigradoListResponse }
  | { type: 'error' }
  | { type: 'forbidden' };

type EstudianteApiState =
  | { type: 'idle' }
  | { type: 'loading' }
  | { type: 'success'; response: ParticipanteBusquedaListResponse }
  | { type: 'error' }
  | { type: 'forbidden' };

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' };
const FORBIDDEN: ApiState = { type: 'forbidden' };

const IDLE_ESTUDIANTE:     EstudianteApiState = { type: 'idle' };
const LOADING_ESTUDIANTE:  EstudianteApiState = { type: 'loading' };
const ERROR_ESTUDIANTE:    EstudianteApiState = { type: 'error' };
const FORBIDDEN_ESTUDIANTE: EstudianteApiState = { type: 'forbidden' };

@Component({
  selector: 'app-cursos-migrados',
  standalone: true,
  imports: [NgIcon, RouterLink, PageTitle, Pagination],
  templateUrl: './cursos-migrados.html',
})
export class CursosMigrados {
  private service = inject(CursoMigradoService);
  private toast   = inject(ToastService);

  searchMode = signal<'curso' | 'estudiante'>('curso');

  searchQuery = signal('');
  mesFiltro   = signal(0);
  participantesMin = signal<number | null>(null);
  participantesMax = signal<number | null>(null);
  pageIndex   = signal(1);
  readonly pageSize = 15;

  readonly meses = [
    { value: 1,  label: 'Enero' },
    { value: 2,  label: 'Febrero' },
    { value: 3,  label: 'Marzo' },
    { value: 4,  label: 'Abril' },
    { value: 5,  label: 'Mayo' },
    { value: 6,  label: 'Junio' },
    { value: 7,  label: 'Julio' },
    { value: 8,  label: 'Agosto' },
    { value: 9,  label: 'Septiembre' },
    { value: 10, label: 'Octubre' },
    { value: 11, label: 'Noviembre' },
    { value: 12, label: 'Diciembre' },
  ];

  estudianteQuery = signal('');
  estudiantePageIndex = signal(1);

  private refreshTick = signal(0);

  stats = toSignal(
    toObservable(this.refreshTick).pipe(
      switchMap(() => this.service.getStats()),
    ),
    { initialValue: null }
  );

  private params = computed(() => ({
    query:            this.searchQuery(),
    mes:              this.mesFiltro(),
    participantesMin: this.participantesMin(),
    participantesMax: this.participantesMax(),
    pageIndex:        this.pageIndex(),
    pageSize:         this.pageSize,
    refreshTick:      this.refreshTick(),
  }));

  private estudianteParams = computed(() => ({
    query:     this.estudianteQuery(),
    pageIndex: this.estudiantePageIndex(),
    pageSize:  this.pageSize,
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
      distinctUntilChanged((a, b) => JSON.stringify(a) === JSON.stringify(b)),
      switchMap(p =>
        this.service.getAll(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith(LOADING),
          catchError((err: HttpErrorResponse) => {
            if (err.status === 403) return of(FORBIDDEN);
            this.toast.error('Error', 'No se pudieron cargar los cursos migrados.');
            return of(ERROR);
          }),
        )
      ),
      startWith(LOADING),
    ),
    { requireSync: true }
  );

  get cursos()    { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get hasError()  { return this.state().type === 'error'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  private estudianteState = toSignal(
    toObservable(this.estudianteParams).pipe(
      debounceTime(300),
      distinctUntilChanged((a, b) => JSON.stringify(a) === JSON.stringify(b)),
      switchMap(p => {
        if (!p.query) return of(IDLE_ESTUDIANTE);
        return this.service.buscarParticipantes(p).pipe(
          map(response => ({ type: 'success', response } as EstudianteApiState)),
          startWith(LOADING_ESTUDIANTE),
          catchError((err: HttpErrorResponse) => {
            if (err.status === 403) return of(FORBIDDEN_ESTUDIANTE);
            this.toast.error('Error', 'No se pudieron buscar los estudiantes.');
            return of(ERROR_ESTUDIANTE);
          }),
        );
      }),
      startWith(IDLE_ESTUDIANTE),
    ),
    { requireSync: true }
  );

  get participantes()      { const s = this.estudianteState(); return s.type === 'success' ? s.response.data : []; }
  get totalEstudiantes()   { const s = this.estudianteState(); return s.type === 'success' ? s.response.total : 0; }
  get isLoadingEstudiante() { return this.estudianteState().type === 'loading'; }
  get errorEstudiante()     { return this.estudianteState().type === 'error'; }
  get forbiddenEstudiante() { return this.estudianteState().type === 'forbidden'; }
  get isIdleEstudiante()    { return this.estudianteState().type === 'idle'; }

  exportingPdf   = signal(false);
  exportingExcel = signal(false);

  exportPdf(): void {
    this.exportingPdf.set(true);
    this.service.exportAllPdf().subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = 'cursos-migrados-listado.pdf';
        a.click();
        URL.revokeObjectURL(url);
        this.exportingPdf.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el PDF.');
        this.exportingPdf.set(false);
      },
    });
  }

  exportExcel(): void {
    this.exportingExcel.set(true);
    this.service.exportAllExcel().subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = 'cursos-migrados-listado.xlsx';
        a.click();
        URL.revokeObjectURL(url);
        this.exportingExcel.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el Excel.');
        this.exportingExcel.set(false);
      },
    });
  }

  qrCurso = signal<CursoMigrado | null>(null);

  abrirQR(curso: CursoMigrado): void {
    this.qrCurso.set(curso);
  }

  cerrarQR(): void {
    this.qrCurso.set(null);
  }

  onSearch(e: Event) {
    this.searchQuery.set((e.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onFiltroMes(e: Event) {
    this.mesFiltro.set(Number((e.target as HTMLSelectElement).value));
    this.pageIndex.set(1);
  }

  onFiltroParticipantesMin(e: Event) {
    const value = (e.target as HTMLInputElement).value;
    this.participantesMin.set(value === '' ? null : Number(value));
    this.pageIndex.set(1);
  }

  onFiltroParticipantesMax(e: Event) {
    const value = (e.target as HTMLInputElement).value;
    this.participantesMax.set(value === '' ? null : Number(value));
    this.pageIndex.set(1);
  }

  onPageChange(page: number) {
    this.pageIndex.set(page);
  }

  setSearchMode(mode: 'curso' | 'estudiante') {
    this.searchMode.set(mode);
  }

  onSearchEstudiante(e: Event) {
    this.estudianteQuery.set((e.target as HTMLInputElement).value);
    this.estudiantePageIndex.set(1);
  }

  onPageChangeEstudiante(page: number) {
    this.estudiantePageIndex.set(page);
  }

  descargandoPdfCurso   = signal<number | null>(null);
  descargandoExcelCurso = signal<number | null>(null);

  descargarPdfCurso(curso: CursoMigrado): void {
    this.descargandoPdfCurso.set(curso.id);
    this.service.exportPdf(curso.id).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `participantes-${curso.slug}.pdf`;
        a.click();
        URL.revokeObjectURL(url);
        this.descargandoPdfCurso.set(null);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el PDF del curso.');
        this.descargandoPdfCurso.set(null);
      },
    });
  }

  descargarExcelCurso(curso: CursoMigrado): void {
    this.descargandoExcelCurso.set(curso.id);
    this.service.exportExcel(curso.id).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `participantes-${curso.slug}.xlsx`;
        a.click();
        URL.revokeObjectURL(url);
        this.descargandoExcelCurso.set(null);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el Excel del curso.');
        this.descargandoExcelCurso.set(null);
      },
    });
  }

  eliminando = signal<number | null>(null);

  confirmarEliminar(curso: CursoMigrado): void {
    if (!confirm(`¿Eliminar el curso "${curso.nombre}"?\nEsta acción eliminará también todos sus participantes y archivos asociados.`)) return;

    this.eliminando.set(curso.id);
    this.service.delete(curso.id).subscribe({
      next: () => {
        this.eliminando.set(null);
        this.toast.success('Listo', `Curso "${curso.nombre}" eliminado correctamente.`);
        this.refreshTick.update(n => n + 1);
      },
      error: () => {
        this.eliminando.set(null);
        this.toast.error('Error', 'No se pudo eliminar el curso.');
      },
    });
  }
}
