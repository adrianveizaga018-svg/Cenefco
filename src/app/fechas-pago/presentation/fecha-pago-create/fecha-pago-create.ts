import { Component, inject, signal, OnInit } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FechaPagoService } from '../../application/services/fecha-pago.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';
import { PlanAcademicoService } from '../../../planes-academicos/application/services/plan-academico.service';
import { PlanAcademico } from '../../../planes-academicos/domain/models/plan-academico.model';

@Component({
  selector: 'app-fecha-pago-create',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './fecha-pago-create.html',
})
export class FechaPagoCreate implements OnInit {
  private service     = inject(FechaPagoService);
  private planService = inject(PlanAcademicoService);
  private toast       = inject(ToastService);
  private router      = inject(Router);
  private route       = inject(ActivatedRoute);
  private fb          = inject(FormBuilder);

  submitting = signal(false);
  planes     = signal<PlanAcademico[]>([]);

  form = this.fb.group({
    id_plan:       [null as number | null, [Validators.required]],
    nro_pago:      [''],
    tipo_tramite:  [''],
    monto_a_pagar: [null as number | null, [Validators.required, Validators.min(0.01)]],
    fecha_inicio:  [''],
    fecha_fin:     [''],
    obligatorio:   [1],
    estado:        [1],
  });

  ngOnInit(): void {
    const qIdPlan = this.route.snapshot.queryParamMap.get('id_plan');
    if (qIdPlan) this.form.patchValue({ id_plan: Number(qIdPlan) });

    this.planService.getAll({ pageSize: 300 }).subscribe({
      next: r => this.planes.set(r.data),
    });
  }

  planLabel(p: PlanAcademico): string {
    let label = p.titulo;
    if (p.convenio)   label += ` — ${p.convenio}`;
    if (p.nro_cuotas) label += ` (${p.nro_cuotas} cuotas)`;
    return label;
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => {
        this.toast.success('¡Creada!', 'Cuota de pago registrada');
        const idPlan = this.form.value.id_plan;
        if (this.route.snapshot.queryParamMap.has('id_plan') && idPlan) {
          this.router.navigate(['/cenefco/plan-edit', idPlan]);
        } else {
          this.router.navigate(['/cenefco/fechas-pago']);
        }
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Error', extractErrorMessage(err));
        this.submitting.set(false);
      }
    });
  }
}
