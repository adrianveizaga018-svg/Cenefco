import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import { InscripcionService } from '../../application/services/inscripcion.service';
import { AcademicoService } from '../../../common/application/services/academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
interface Imparticion  { id_imp: number; periodo: string | null; gestion: string | null; materia_nombre: string | null; paralelo: string | null; id_mat: number | null; docente_nombre: string | null; }
interface UsuarioOption { id_us: number; nombre: string; appaterno: string | null; ci: string | null; }
interface PlanOption    { id_plan: number; titulo: string; convenio: string | null; nro_cuotas: number; costo: string | null; }

const CANALES_VENTA = [
  { value: 'admin',    label: 'Presencial (admin)' },
  { value: 'portal',   label: 'Portal web' },
  { value: 'whatsapp', label: 'WhatsApp' },
  { value: 'referido', label: 'Referido' },
];

@Component({
  selector: 'app-inscripcion-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './inscripcion-create.html',
})
export class InscripcionCreate implements OnInit {
  private service      = inject(InscripcionService);
  private academicoSvc = inject(AcademicoService);
  private toast        = inject(ToastService);
  private router       = inject(Router);
  private fb           = inject(FormBuilder);

  submitting    = signal(false);
  usuarios      = signal<UsuarioOption[]>([]);
  imparticiones = signal<Imparticion[]>([]);
  planes        = signal<PlanOption[]>([]);
  loadingPlanes = signal(false);
  readonly canales = CANALES_VENTA;

  form = this.fb.group({
    id_us:           [null as number | null, [Validators.required]],
    id_imp:          [null as number | null, [Validators.required]],
    id_plan:         [null as number | null],
    fecha_ins:       [new Date().toISOString().split('T')[0]],
    periodo:         [''],
    gestion:         [new Date().getFullYear().toString()],
    observacion_ins: [''],
    estado:          [1],
    id_vendedor:     [null as number | null],
    canal_venta:     ['admin'],
  });

  ngOnInit(): void {
    this.academicoSvc.getUsuariosAcademicos({ pageSize: 300, pageIndex: 1, conInactivos: true })
      .subscribe({ next: r => this.usuarios.set(r.data) });

    this.academicoSvc.getImparticiones({ pageSize: 200, pageIndex: 1, conInactivos: true })
      .subscribe({ next: r => this.imparticiones.set(r.data) });
  }

  usuarioLabel(u: UsuarioOption): string {
    return `${u.nombre} ${u.appaterno ?? ''}${u.ci ? ' — CI: ' + u.ci : ''}`.trim();
  }

  imparticionLabel(imp: Imparticion): string {
    const mat = imp.materia_nombre ?? `ID ${imp.id_mat}`;
    const doc = imp.docente_nombre?.trim() || '';
    return `[${imp.periodo}] ${mat}${doc ? ' — ' + doc : ''}`;
  }

  planLabel(p: PlanOption): string {
    const cuotas = p.nro_cuotas ? ` · ${p.nro_cuotas} cuotas` : '';
    const costo  = p.costo ? ` · Bs. ${p.costo}` : '';
    return `${p.titulo}${cuotas}${costo}`;
  }

  onImparticionChange(event: Event): void {
    const id = Number((event.target as HTMLSelectElement).value);
    const imp = this.imparticiones().find(i => i.id_imp === id);
    if (imp) {
      this.form.patchValue({ periodo: imp.periodo ?? '', gestion: imp.gestion ?? '' });
      if (imp.id_mat) {
        this.loadingPlanes.set(true);
        this.planes.set([]);
        this.form.patchValue({ id_plan: null });
        this.academicoSvc.getPlanesByMateria(imp.id_mat, { pageSize: 50 })
          .subscribe({
            next: r => { this.planes.set(r.data); this.loadingPlanes.set(false); },
            error: () => this.loadingPlanes.set(false),
          });
      }
    }
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: (res) => {
        this.toast.success('¡Creada!', 'Inscripción registrada correctamente');
        if (res?.id_ins) {
          this.router.navigate(['/cenefco/inscripcion-detail', res.id_ins]);
        } else {
          this.router.navigate(['/cenefco/inscripciones']);
        }
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err));
        this.submitting.set(false);
      },
    });
  }
}
