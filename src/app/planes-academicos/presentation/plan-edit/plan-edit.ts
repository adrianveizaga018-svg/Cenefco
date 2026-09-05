import { Component, inject, signal, computed, ChangeDetectorRef } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { NgSelectModule } from '@ng-select/ng-select';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PlanAcademicoService } from '../../application/services/plan-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';
import { FechaPagoService } from '../../../fechas-pago/application/services/fecha-pago.service';
import { FechaPago } from '../../../fechas-pago/domain/models/fecha-pago.model';
import Swal from 'sweetalert2';
import { ConvenioService } from '../../../convenios/application/services/convenio.service';
import { ConvenioOption } from '../../../convenios/domain/models/convenio.model';

@Component({
  selector: 'app-plan-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle, NgSelectModule],
  templateUrl: './plan-edit.html',
})
export class PlanEdit {
  private service         = inject(PlanAcademicoService);
  private convenioService = inject(ConvenioService);
  private fechaService    = inject(FechaPagoService);
  private toast           = inject(ToastService);
  private router          = inject(Router);
  private route           = inject(ActivatedRoute);
  private fb              = inject(FormBuilder);
  private cdr             = inject(ChangeDetectorRef);

  submitting    = signal(false);
  loading       = signal(true);
  cuotas        = signal<FechaPago[]>([]);
  loadingCuotas = signal(false);
  mostrarForm   = signal(false);
  guardandoCuota= signal(false);
  convenios     = signal<ConvenioOption[]>([]);
  editandoCuotaId  = signal<number | null>(null);
  nroCuotasInput   = signal('');

  cuotasLlenas = computed(() => {
    const max = parseInt(this.nroCuotasInput(), 10);
    return max > 0 && this.cuotas().length >= max;
  });

  cuotasExcedidas = computed(() => {
    const max = parseInt(this.nroCuotasInput(), 10);
    return max > 0 && this.cuotas().length > max;
  });

  id = Number(this.route.snapshot.paramMap.get('id'));

  form = this.fb.group({
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

  cuotaForm = this.fb.group({
    id_fechapago:  [Math.floor(Date.now() / 1000)],
    nro_pago:      [''],
    tipo_tramite:  [''],
    monto_a_pagar: ['', [Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
    fecha_inicio:  [''],
    fecha_fin:     [''],
    obligatorio:   [1],
    estado:        [1],
  });

  constructor() {
    this.convenioService.getAll$().subscribe({
      next: list => { this.convenios.set(list); this.cdr.detectChanges(); },
      error: () => {},
    });

    const calcCuota = () => {
      const costo  = parseFloat(this.form.get('costo')!.value ?? '');
      const cuotas = parseInt(this.form.get('nro_cuotas')!.value ?? '', 10);
      if (costo > 0 && cuotas > 0) {
        const resultado = (costo / cuotas).toFixed(2);
        this.form.get('costo_por_cuota')!.setValue(resultado, { emitEvent: false });
      }
    };

    this.form.get('costo')!.valueChanges.subscribe(calcCuota);
    this.form.get('nro_cuotas')!.valueChanges.subscribe(v => {
      this.nroCuotasInput.set(v ?? '');
      calcCuota();
      const max = parseInt(v ?? '', 10);
      const registradas = this.cuotas().length;
      if (max > 0 && registradas > max) {
        Swal.fire({
          icon: 'warning',
          title: 'Cuotas excedidas',
          html: `Tiene <strong>${registradas}</strong> cuota(s) registrada(s).<br>Debe eliminar <strong>${registradas - max}</strong> cuota(s) antes de reducir el N° de Cuotas a <strong>${max}</strong>.`,
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#f59e0b',
        });
      }
    });

    this.service.getById(this.id).subscribe({
      next: d => {
        this.form.patchValue(d as any);
        this.nroCuotasInput.set(String((d as any).nro_cuotas ?? ''));
        this.loading.set(false);
        this.cargarCuotas();
      },
      error: () => { this.toast.error('Error', 'No se pudo cargar'); this.router.navigate(['/cenefco/planes-academicos']); }
    });
  }

  cargarCuotas(): void {
    this.loadingCuotas.set(true);
    this.fechaService.getAll({ id_plan: this.id, pageSize: 100, conInactivos: false }).subscribe({
      next: r => { this.cuotas.set(r.data); this.loadingCuotas.set(false); this.cdr.detectChanges(); },
      error: () => { this.loadingCuotas.set(false); this.cdr.detectChanges(); },
    });
  }

  abrirFormCuota(): void {
    const siguienteNro  = this.cuotas().length + 1;
    const costoCuota    = this.form.get('costo_por_cuota')!.value ?? '';

    this.editandoCuotaId.set(null);
    this.cuotaForm.reset({
      id_fechapago:  Math.floor(Date.now() / 1000),
      nro_pago:      String(siguienteNro),
      monto_a_pagar: costoCuota,
      obligatorio:   1,
      estado:        1,
    });
    this.mostrarForm.set(true);
  }

  editarCuota(cuota: FechaPago): void {
    this.editandoCuotaId.set(cuota.id_fechapago);
    this.cuotaForm.reset({
      id_fechapago:  cuota.id_fechapago,
      nro_pago:      cuota.nro_pago ?? '',
      tipo_tramite:  cuota.tipo_tramite ?? '',
      monto_a_pagar: cuota.monto_a_pagar != null ? String(cuota.monto_a_pagar) : '',
      fecha_inicio:  cuota.fecha_inicio ?? '',
      fecha_fin:     cuota.fecha_fin ?? '',
      obligatorio:   cuota.obligatorio,
      estado:        cuota.estado,
    } as any);
    this.mostrarForm.set(true);
  }

  guardarCuota(): void {
    this.guardandoCuota.set(true);
    const editId = this.editandoCuotaId();

    if (editId !== null) {
      const payload = { ...this.cuotaForm.value, id_plan: this.id };
      this.fechaService.update(editId, payload as any).subscribe({
        next: () => {
          this.toast.success('¡Actualizada!', 'Cuota actualizada correctamente');
          this.guardandoCuota.set(false);
          this.mostrarForm.set(false);
          this.editandoCuotaId.set(null);
          this.cargarCuotas();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar'));
          this.guardandoCuota.set(false);
        }
      });
    } else {
      const payload = { ...this.cuotaForm.value, id_plan: this.id };
      this.fechaService.create(payload as any).subscribe({
        next: () => {
          this.toast.success('¡Agregada!', 'Cuota registrada correctamente');
          this.guardandoCuota.set(false);
          this.mostrarForm.set(false);
          this.cargarCuotas();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo guardar'));
          this.guardandoCuota.set(false);
        }
      });
    }
  }

  eliminarCuota(cuota: FechaPago): void {
    Swal.fire({
      title: '¿Eliminar cuota?',
      html: `
        <p class="mb-3">Cuota: <strong>${cuota.nro_pago ?? '#' + cuota.id_fechapago}</strong></p>
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:10px 14px;text-align:left;font-size:13px;color:#856404;">
          <strong>⚠ Importante:</strong> Si ya existen pagos registrados asociados a esta cuota, eliminarla puede generar inconsistencias en los registros académicos y financieros. Proceda con cuidado.
        </div>
      `,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.fechaService.delete(cuota.id_fechapago).subscribe({
          next: () => {
            this.toast.success('Eliminada', 'Cuota eliminada correctamente');
            this.cargarCuotas();
            this.cdr.detectChanges();
          },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar la cuota')),
        });
      }
    });
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    if (this.cuotasExcedidas()) { this.toast.error('Error de validación', `Ya tiene ${this.cuotas().length} cuotas registradas. No puede reducir N° de Cuotas a ${this.nroCuotasInput()}.`); return; }
    this.submitting.set(true);
    this.service.update(this.id, this.form.value as any).subscribe({
      next: () => { this.toast.success('¡Actualizado!', 'Plan actualizado'); this.router.navigate(['/cenefco/planes-academicos']); },
      error: (err: HttpErrorResponse) => { this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar')); this.submitting.set(false); }
    });
  }
}
