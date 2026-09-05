import { ChangeDetectorRef, Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { HttpErrorResponse } from '@angular/common/http';
import { ConfiguracionAcademicaService } from '../../application/services/configuracion-academica.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';

@Component({
  selector: 'app-configuracion-academica-edit',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './configuracion-academica-edit.html',
})
export class ConfiguracionAcademicaEdit {
  private service = inject(ConfiguracionAcademicaService); private toast = inject(ToastService);
  private router  = inject(Router); private route = inject(ActivatedRoute);
  private fb      = inject(FormBuilder); private cdr = inject(ChangeDetectorRef);
  submitting = signal(false); loading = signal(true);
  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
    gestion: [''], periodo_est: [''], gestion_est: [''], max_materias_cursar: [''],
    id_plan: [null as number | null], id_plan_anterior: [null as number | null],
    periodo_doc: [''], gestion_doc: [''], correlativo: [''], nombre_kardista: [''],
    nombre_director: [''], titulo_carrera: [''], descripcion_resolucion: [''],
    cod_codigo: [''], lugar_x: [''], carrera: [''], area: [''], periodo: [''], estado: [1],
  });

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => { this.form.patchValue(d as any); this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/configuracion-academica']); },
    });
  }

  onSubmit(): void {
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Configuración actualizada'); this.router.navigate(['/cenefco/configuracion-academica']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); },
    });
  }
}
