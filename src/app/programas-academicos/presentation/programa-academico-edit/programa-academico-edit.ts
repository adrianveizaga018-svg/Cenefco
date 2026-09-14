import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { ProgramaAcademicoService } from '../../application/services/programa-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';

@Component({ selector: 'app-programa-academico-edit', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle], templateUrl: './programa-academico-edit.html' })
export class ProgramaAcademicoEdit implements OnInit {
  private service = inject(ProgramaAcademicoService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute); private fb = inject(FormBuilder);
  
  submitting = signal(false); 
  loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  // TABS
  activeTab = signal<'general' | 'versiones' | 'planes'>('general');

  // FORM GENERAL
  form = this.fb.group({
    nombre_programa:          ['', [Validators.required, Validators.maxLength(200)]],
    descripcion:              [''], dirigido: [''], inversion: [''],
    requisitos:               [''], creditaje: [''], objetivo: [''], nota: [''],
    id_tipoprograma:          [null as number | null], url_video: [''],
    inicio_actividades:       [''], finalizacion_actividades: [''], inicio_inscripciones: [''],
    estado:                   [1],
    estado_web:               ['borrador'],
  });

  // VERSIONES (IMPARTICIONES)
  imparticiones = signal<any[]>([]);
  showVersionModal = signal(false);
  versionEditId = signal<number | null>(null);
  formVersion = this.fb.group({
    nombre: ['', Validators.required],
    periodo: [''],
    gestion: [new Date().getFullYear(), Validators.required],
    imparte_fecha_inicio: [''],
    imparte_fecha_fin: [''],
    estado: [1]
  });

  // PLANES
  todosLosPlanes = signal<any[]>([]);
  planesHabilitados = signal<number[]>([]);
  submittingPlanes = signal(false);

  ngOnInit() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/programas-academicos']); }
    });
    this.cargarImparticiones();
    this.cargarPlanes();
  }

  // --- MÉTODOS GENERAL ---
  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Programa actualizado'); this.router.navigate(['/cenefco/programas-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }

  // --- MÉTODOS VERSIONES ---
  cargarImparticiones() {
    this.service.getImparticiones(this.id).subscribe(res => this.imparticiones.set(res));
  }

  abrirNuevaVersion() {
    this.versionEditId.set(null);
    this.formVersion.reset({ gestion: new Date().getFullYear(), estado: 1 });
    this.showVersionModal.set(true);
  }

  abrirEditarVersion(imp: any) {
    this.versionEditId.set(imp.id_imp);
    this.formVersion.patchValue(imp);
    this.showVersionModal.set(true);
  }

  guardarVersion() {
    if (this.formVersion.invalid) return;
    const vId = this.versionEditId();
    const req = vId 
      ? this.service.updateImparticion(this.id, vId, this.formVersion.value)
      : this.service.createImparticion(this.id, this.formVersion.value);
    
    req.subscribe({
      next: () => {
        this.toast.success('Éxito', 'Versión guardada');
        this.showVersionModal.set(false);
        this.cargarImparticiones();
      },
      error: (e) => this.toast.error('Error', extractErrorMessage(e))
    });
  }

  // --- MÉTODOS PLANES ---
  cargarPlanes() {
    this.service.getPlanes(this.id).subscribe(res => {
      this.todosLosPlanes.set(res.todos_los_planes);
      this.planesHabilitados.set(res.planes_habilitados.map((p: any) => p.id_plan));
    });
  }

  togglePlan(planId: number) {
    const current = this.planesHabilitados();
    if (current.includes(planId)) {
      this.planesHabilitados.set(current.filter(id => id !== planId));
    } else {
      this.planesHabilitados.set([...current, planId]);
    }
  }

  guardarPlanes() {
    this.submittingPlanes.set(true);
    this.service.syncPlanes(this.id, this.planesHabilitados()).subscribe({
      next: () => {
        this.toast.success('Éxito', 'Planes actualizados');
        this.submittingPlanes.set(false);
      },
      error: (e) => {
        this.toast.error('Error', extractErrorMessage(e));
        this.submittingPlanes.set(false);
      }
    });
  }
}
