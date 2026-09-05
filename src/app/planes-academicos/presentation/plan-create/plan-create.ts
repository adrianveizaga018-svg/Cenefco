import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { NgSelectModule } from '@ng-select/ng-select';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PlanAcademicoService } from '../../application/services/plan-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';
import { ConvenioService } from '../../../convenios/application/services/convenio.service';
import { ConvenioOption } from '../../../convenios/domain/models/convenio.model';
import { ChangeDetectorRef } from '@angular/core';

@Component({ selector: 'app-plan-create', imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle, NgSelectModule], templateUrl: './plan-create.html' })
export class PlanCreate {
  private service         = inject(PlanAcademicoService);
  private convenioService = inject(ConvenioService);
  private toast           = inject(ToastService);
  private router          = inject(Router);
  private fb              = inject(FormBuilder);
  private cdr             = inject(ChangeDetectorRef);

  submitting  = signal(false);
  convenios   = signal<ConvenioOption[]>([]);
  private autoId = Math.floor(Date.now() / 1000);

  form = this.fb.group({
    id_plan:           [this.autoId, [Validators.required]],
    titulo:            ['', [Validators.required, Validators.maxLength(200)]],
    titulo_plan:       ['', [Validators.maxLength(200)]],
    convenio:          [''],
    convenio_id:       [null as number | null],
    anio:              ['', [Validators.pattern(/^\d{4}$/)]],
    numero_resolucion: ['', [Validators.maxLength(100)]],
    costo:             ['', [Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
    nro_cuotas:        ['', [Validators.pattern(/^\d+$/)]],
    descuento:         ['', [Validators.pattern(/^(100(\.0{1,2})?|[0-9]{1,2}(\.\d{1,2})?)$/)]],
    costo_por_cuota:   ['', [Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
    id_catplan:        [null as number | null],
    estado:            [1],
  });

  constructor() {
    this.convenioService.getAll$().subscribe({
      next: list => { this.convenios.set(list); this.cdr.detectChanges(); },
      error: () => {},
    });

    const calcCuota = () => {
      const costo     = parseFloat(this.form.get('costo')!.value ?? '');
      const cuotas    = parseInt(this.form.get('nro_cuotas')!.value ?? '', 10);
      if (costo > 0 && cuotas > 0) {
        const resultado = (costo / cuotas).toFixed(2);
        this.form.get('costo_por_cuota')!.setValue(resultado, { emitEvent: false });
      }
    };

    this.form.get('costo')!.valueChanges.subscribe(calcCuota);
    this.form.get('nro_cuotas')!.valueChanges.subscribe(calcCuota);
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.submitting.set(true);
    this.service.create(this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Creado!', 'Plan académico registrado'); this.router.navigate(['/cenefco/planes-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar')); this.submitting.set(false); }
    });
  }
}
