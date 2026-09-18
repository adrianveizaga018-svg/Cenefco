import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators, ValidatorFn, AbstractControl, ValidationErrors } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { forkJoin } from 'rxjs';
import { ProgramaAcademicoService } from '../../application/services/programa-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

const fechasVersionValidator: ValidatorFn = (control: AbstractControl): ValidationErrors | null => {
  const inicioInsc = control.get('inicio_inscripciones')?.value;
  const inicioAct = control.get('imparte_fecha_inicio')?.value;
  const finAct = control.get('imparte_fecha_fin')?.value;
  if (!inicioInsc && !inicioAct && !finAct) return null;
  const errors: Record<string, boolean> = {};
  if (inicioInsc && inicioAct && new Date(inicioInsc) > new Date(inicioAct)) {
    errors['inscripcionTardia'] = true;
  }
  if (inicioAct && finAct && new Date(inicioAct) > new Date(finAct)) {
    errors['finTemprano'] = true;
  }
  return Object.keys(errors).length ? errors : null;
};

@Component({
  selector: 'app-version-imparticion-form',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './version-imparticion-form.html',
})
export class VersionImparticionForm implements OnInit {
  private service = inject(ProgramaAcademicoService);
  private toast = inject(ToastService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private fb = inject(FormBuilder);

  idPrograma = Number(this.route.snapshot.paramMap.get('id'));
  idImp = this.route.snapshot.paramMap.get('idImp');
  esEdicion = !!this.idImp;
  loading = signal(true);
  submitting = signal(false);
  nombrePrograma = signal('');
  todosLosPlanes = signal<any[]>([]);
  planesHabilitados = signal<number[]>([]);
  planesVersion = signal<number[]>([]);
  busquedaPlanes = signal('');
  planesFiltrados = computed(() => {
    const q = this.busquedaPlanes().trim().toLowerCase();
    const lista = this.todosLosPlanes();
    if (!q) return lista;
    return lista.filter(p => String(p.titulo ?? '').toLowerCase().includes(q));
  });
  anioGestion = computed(() => Number(this.form.get('gestion')?.value) || new Date().getFullYear());

  form = this.fb.group({
    nombre: ['', Validators.required],
    periodo: ['I', Validators.required],
    gestion: [new Date().getFullYear(), Validators.required],
    imparte_fecha_inicio: ['', Validators.required],
    imparte_fecha_fin: ['', Validators.required],
    inicio_inscripciones: [''],
  }, { validators: fechasVersionValidator });

  ngOnInit(): void {
    forkJoin({
      programa: this.service.getById(this.idPrograma),
      imparticiones: this.service.getImparticiones(this.idPrograma),
      planes: this.service.getPlanes(this.idPrograma),
    }).subscribe({
      next: ({ programa, imparticiones, planes }) => {
        this.nombrePrograma.set(programa.nombre_programa ?? '');
        this.todosLosPlanes.set(planes.todos_los_planes || []);
        const habilitados = (planes.planes_habilitados || []).map((p: any) => Number(p.id_plan));
        this.planesHabilitados.set(habilitados);

        // Validador dinámico — ignora acentos para evitar falsos duplicados
        const normalizar = (s: string) => s.trim().toLowerCase()
          .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        this.form.get('nombre')?.addValidators((control: AbstractControl) => {
          const val = normalizar(String(control.value || ''));
          const existe = imparticiones.some((i: any) => {
            if (this.esEdicion && String(i.id_imp) === String(this.idImp)) return false;
            return normalizar(String(i.nombre || '')) === val;
          });
          return existe ? { nombreDuplicado: true } : null;
        });
        this.form.get('nombre')?.updateValueAndValidity();

        if (!this.esEdicion) {
          // Find the max number from existing versions to avoid duplicates if one was deleted
          let maxVersion = imparticiones.length;
          imparticiones.forEach((imp: any) => {
            const match = String(imp.nombre || '').match(/Versi[oó]n\s+(\d+)/i);
            if (match && Number(match[1]) > maxVersion) {
              maxVersion = Number(match[1]);
            }
          });
          
          this.form.patchValue({ nombre: `Versión ${maxVersion + 1}` });
          // Default to the planes enabled for the program
          this.planesVersion.set([...habilitados]);
        }

        if (this.esEdicion) {
          const imp = imparticiones.find((i: any) => String(i.id_imp) === String(this.idImp));
          if (!imp) {
            this.toast.error('Error', 'No se encontró la versión');
            this.volver();
            return;
          }
          this.form.patchValue({
            nombre: imp.nombre || '',
            periodo: this.normalizarSemestre(imp.periodo),
            gestion: Number(imp.gestion) || new Date().getFullYear(),
            imparte_fecha_inicio: this.soloFecha(imp.imparte_fecha_inicio),
            imparte_fecha_fin: this.soloFecha(imp.imparte_fecha_fin),
            inicio_inscripciones: this.soloFecha((programa as any).inicio_inscripciones),
          });
          this.planesVersion.set(
            Array.isArray(imp.planes) && imp.planes.length
              ? imp.planes.map((id: number) => Number(id))
              : [...habilitados]
          );
        } else {
          const n = imparticiones.length + 1;
          const mes = new Date().getMonth() + 1;
          this.form.patchValue({
            nombre: 'Versión ' + n,
            periodo: mes <= 6 ? 'I' : 'II',
            gestion: new Date().getFullYear(),
          });
          this.planesVersion.set([...habilitados]);
        }
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar');
        this.volver();
      },
    });
  }

  togglePlan(planId: number): void {
    const current = this.planesVersion();
    this.planesVersion.set(
      current.includes(planId) ? current.filter(id => id !== planId) : [...current, planId]
    );
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    const payload = { ...this.form.value, planes: this.planesVersion() };
    const req = this.esEdicion
      ? this.service.updateImparticion(this.idPrograma, Number(this.idImp), payload)
      : this.service.createImparticion(this.idPrograma, payload);

    req.subscribe({
      next: () => {
        this.toast.success('¡Guardado!', this.esEdicion ? 'Versión actualizada' : 'Versión registrada');
        this.volver();
      },
      error: (e) => {
        this.toast.error('Error', extractErrorMessage(e, 'No se pudo guardar'));
        this.submitting.set(false);
      },
    });
  }

  volver(): void {
    this.router.navigate(['/cenefco/programa-academico-edit', this.idPrograma]);
  }

  private normalizarSemestre(valor: string | null | undefined): string {
    const t = String(valor ?? '').toUpperCase();
    if (t.includes('II') || t === '2') return 'II';
    return 'I';
  }

  private soloFecha(valor: string | null | undefined): string {
    if (!valor) return '';
    return String(valor).slice(0, 10);
  }
}
