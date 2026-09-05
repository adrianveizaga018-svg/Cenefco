import { Component, inject, signal, computed, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { CartaGeneradaService } from '../../application/services/carta-generada.service';
import { CartaModeloService } from '../../../cartas-modelo/application/services/carta-modelo.service';
import { CartaModelo } from '../../../cartas-modelo/domain/models/carta-modelo.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

interface CursoOption  { id_programa: number; nombre_programa: string; }
interface AlumnoOption { id_us: number; nombre: string; appaterno: string | null; ci: string | null; }

@Component({
  selector: 'app-carta-generada-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './carta-generada-create.html'
})
export class CartaGeneradaCreate implements OnInit {
  private service   = inject(CartaGeneradaService);
  private toast     = inject(ToastService);
  private router    = inject(Router);
  private route     = inject(ActivatedRoute);
  private fb        = inject(FormBuilder);
  private http      = inject(HttpClient);
  private modeloSvc = inject(CartaModeloService);

  submitting        = signal(false);
  vistaActiva       = signal<'formulario' | 'preview'>('formulario');
  fechaHoy          = new Date().toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
  private autoId    = Math.floor(Date.now() / 1000);

  cursos            = signal<CursoOption[]>([]);
  todosAlumnos      = signal<AlumnoOption[]>([]);
  inscritosIds      = signal<Set<number>>(new Set());
  modelos           = signal<CartaModelo[]>([]);

  cargandoCursos    = signal(true);
  cargandoAlumnos   = signal(true);
  cargandoInscritos = signal(false);

  alumnoSeleccionado = signal<AlumnoOption | null>(null);
  cursoSeleccionado  = signal<CursoOption | null>(null);
  modeloBase         = signal<CartaModelo | null>(null);

  alumnosFiltrados = computed(() => {
    const ids = this.inscritosIds();
    if (ids.size === 0) return this.todosAlumnos();
    return this.todosAlumnos().filter(a => ids.has(a.id_us));
  });

  form = this.fb.group({
    id_cartagen:                  [this.autoId, [Validators.required]],
    num_carta:                    [this.autoId],
    id_us:                        [null as number | null, [Validators.required]],
    id_cartamod:                  [null as number | null, [Validators.required]],
    textocarta:                   [''],
    textocarta1:                  [''],
    textocarta3:                  [''],
    usar_encabezado_pie_estandar: [1],
    cp_nro_contrato:              [null as number | null],
    cp_gestion_contrato:          [''],
    estado:                       [1],
  });

  ngOnInit(): void {
    this.http.get<{ data: CursoOption[] }>('/api/v1/cursos', {
      params: { pageSize: '200', pageIndex: '1' }
    }).subscribe({ next: r => { this.cursos.set(r.data); this.cargandoCursos.set(false); }, error: () => this.cargandoCursos.set(false) });

    this.http.get<{ data: AlumnoOption[] }>('/api/v1/usuarios-academicos', {
      params: { pageSize: '300', pageIndex: '1', conInactivos: 'true' }
    }).subscribe({
      next: r => {
        this.todosAlumnos.set(r.data);
        this.cargandoAlumnos.set(false);
        const idUs = this.route.snapshot.queryParamMap.get('id_us');
        if (idUs) {
          const alumno = r.data.find(a => a.id_us === Number(idUs));
          if (alumno) { this.alumnoSeleccionado.set(alumno); this.form.patchValue({ id_us: alumno.id_us }); this.aplicarSustitucion(); }
        }
      },
      error: () => this.cargandoAlumnos.set(false)
    });

    const idCartamod = this.route.snapshot.queryParamMap.get('id_cartamod');
    this.modeloSvc.getAll({ pageSize: 100 }).subscribe({
      next: r => {
        this.modelos.set(r.data);
        if (idCartamod) {
          const modelo = r.data.find(m => m.id_cartamod === Number(idCartamod)) ?? null;
          if (modelo) {
            this.form.patchValue({
              id_cartamod: modelo.id_cartamod,
              textocarta:  modelo.textocarta  ?? modelo.texto_carta ?? '',
              textocarta1: modelo.textocarta1 ?? '',
              textocarta3: modelo.textocarta3 ?? '',
              usar_encabezado_pie_estandar: modelo.usar_encabezado_pie_estandar,
            });
            this.modeloBase.set(modelo);
            this.aplicarSustitucion();
          }
        }
      }
    });

    const programaId = this.route.snapshot.queryParamMap.get('programa_id');
    if (programaId) this.cargarInscritos(Number(programaId));
  }

  onDiplomadoChange(event: Event): void {
    const val = (event.target as HTMLSelectElement).value;
    if (!val) {
      this.cursoSeleccionado.set(null);
      this.inscritosIds.set(new Set());
    } else {
      const curso = this.cursos().find(c => c.id_programa === Number(val)) ?? null;
      this.cursoSeleccionado.set(curso);
      this.cargarInscritos(Number(val));
      this.aplicarSustitucion();
    }
  }

  private cargarInscritos(programaId: number): void {
    this.cargandoInscritos.set(true);
    this.inscritosIds.set(new Set());
    this.http.get<{ data: { id_us: number }[] }>('/api/v1/inscripciones', {
      params: { programa_id: programaId.toString(), pageSize: '300', pageIndex: '1' }
    }).subscribe({
      next: r => { this.inscritosIds.set(new Set(r.data.map(i => i.id_us))); this.cargandoInscritos.set(false); },
      error: () => this.cargandoInscritos.set(false),
    });
  }

  onAlumnoChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    const alumno = this.todosAlumnos().find(a => a.id_us === id) ?? null;
    this.alumnoSeleccionado.set(alumno);
    this.form.patchValue({ id_us: id || null });
    this.aplicarSustitucion();
  }

  onModeloChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    const modelo = this.modelos().find(m => m.id_cartamod === id) ?? null;
    this.modeloBase.set(modelo);
    if (modelo) {
      this.form.patchValue({
        textocarta:  modelo.textocarta  ?? modelo.texto_carta ?? '',
        textocarta1: modelo.textocarta1 ?? '',
        textocarta3: modelo.textocarta3 ?? '',
        usar_encabezado_pie_estandar: modelo.usar_encabezado_pie_estandar,
      });
      this.aplicarSustitucion();
    }
  }

  private aplicarSustitucion(): void {
    const modelo = this.modeloBase();
    if (!modelo) return;

    const alumno = this.alumnoSeleccionado();
    const curso  = this.cursoSeleccionado();
    const nombre = alumno ? `${alumno.nombre} ${alumno.appaterno ?? ''}`.trim() : '{{nombre}}';
    const cursoNombre = curso ? curso.nombre_programa : '{{curso}}';

    const sustituir = (txt: string | null) =>
      (txt ?? '').replace(/\{\{nombre\}\}/gi, nombre).replace(/\{\{curso\}\}/gi, cursoNombre);

    this.form.patchValue({
      textocarta:  sustituir(modelo.textocarta  ?? modelo.texto_carta ?? ''),
      textocarta1: sustituir(modelo.textocarta1 ?? ''),
      textocarta3: sustituir(modelo.textocarta3 ?? ''),
    });
  }

  alumnoLabel(a: AlumnoOption): string {
    return `${a.nombre} ${a.appaterno ?? ''}${a.ci ? ' — CI: ' + a.ci : ''}`.trim();
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: (res) => { this.toast.success('¡Generada!', 'Carta generada correctamente'); this.router.navigate(['/cenefco/carta-generada-detail', res.id_cartagen]); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
