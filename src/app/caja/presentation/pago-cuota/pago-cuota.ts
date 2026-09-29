import { Component, inject, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators, FormBuilder } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CajaService, CajaBanco } from '../../application/services/caja.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';
import { Title } from '@angular/platform-browser';

@Component({
  selector: 'app-pago-cuota',
  standalone: true,
  imports: [ReactiveFormsModule, NgIcon, PageTitle],
  templateUrl: './pago-cuota.html',
})
export class PagoCuota {
  private cajaService = inject(CajaService);
  private toast       = inject(ToastService);
  private fb          = inject(FormBuilder);
  private titleService = inject(Title);

  ciControl = new FormControl('');
  buscando = signal(false);
  
  estudiante = signal<any | null>(null);
  cuotas = signal<any[]>([]);
  bancos = signal<CajaBanco[]>([]);

  cuotaSeleccionada = signal<any | null>(null);
  procesando = signal(false);
  comprobanteFile: File | null = null;

  formPago = this.fb.group({
    metodo_pago: ['efectivo', Validators.required],
    tipo_banco_id: [''],
    nro_boleta: [''],
    fecha_deposito: [new Date().toISOString().split('T')[0], Validators.required],
  });

  constructor() {
    this.titleService.setTitle('Registrar Pago de Cuota - CENEFCO');
    
    // Cargar bancos para el form
    this.cajaService.getBancos().subscribe({
      next: (b) => this.bancos.set(b)
    });

    // Validadores dinámicos según método de pago
    this.formPago.get('metodo_pago')?.valueChanges.subscribe(metodo => {
      const bDestinoControl = this.formPago.get('tipo_banco_id');
      const nroBoletaControl = this.formPago.get('nro_boleta');
      
      if (metodo === 'deposito' || metodo === 'transferencia') {
        bDestinoControl?.setValidators([Validators.required]);
      } else {
        bDestinoControl?.clearValidators();
      }

      if (metodo !== 'efectivo') {
        nroBoletaControl?.setValidators([Validators.required]);
      } else {
        nroBoletaControl?.clearValidators();
      }

      bDestinoControl?.updateValueAndValidity();
      nroBoletaControl?.updateValueAndValidity();
    });
  }

  buscar() {
    const ci = this.ciControl.value?.trim();
    if (!ci) return;

    this.buscando.set(true);
    this.estudiante.set(null);
    this.cuotas.set([]);

    this.cajaService.getCuotasPendientes(ci).subscribe({
      next: (res) => {
        this.estudiante.set(res.estudiante);
        this.cuotas.set(res.cuotas);
        this.buscando.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.toast.error('Atención', err.status === 404 ? 'Estudiante no encontrado o sin cuotas pendientes' : 'Error al buscar cuotas');
        this.buscando.set(false);
      }
    });
  }

  isVencida(fecha: string): boolean {
    if (!fecha) return false;
    const hoy = new Date();
    hoy.setHours(0,0,0,0);
    const f = new Date(fecha);
    f.setHours(0,0,0,0);
    return f < hoy;
  }

  abrirModalPago(cuota: any) {
    this.cuotaSeleccionada.set(cuota);
    this.comprobanteFile = null;
    this.formPago.reset({
      metodo_pago: 'efectivo',
      tipo_banco_id: '',
      nro_boleta: '',
      fecha_deposito: new Date().toISOString().split('T')[0]
    });
  }

  cerrarModal() {
    this.cuotaSeleccionada.set(null);
  }

  onFileChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    this.comprobanteFile = file || null;
  }

  confirmarPago() {
    if (this.formPago.invalid) {
      this.formPago.markAllAsTouched();
      return;
    }

    this.procesando.set(true);
    const formVals = this.formPago.value;
    const payload = {
      id_cuota: this.cuotaSeleccionada()!.id,
      monto_pagado: this.cuotaSeleccionada()!.monto_a_pagar,
      metodo_pago: formVals.metodo_pago,
      fecha_deposito: formVals.fecha_deposito,
      nro_boleta: formVals.nro_boleta || 'Efectivo',
      tipo_banco_id: formVals.tipo_banco_id || ''
    };

    this.cajaService.registrarPagoCuota(payload, this.comprobanteFile).subscribe({
      next: () => {
        this.toast.success('¡Éxito!', 'Pago de cuota registrado correctamente');
        this.procesando.set(false);
        this.cerrarModal();
        this.buscar(); // Recargar la lista
      },
      error: (err: HttpErrorResponse) => {
        this.procesando.set(false);
        if (err.status === 422 && err.error?.boleta_duplicada) {
          const d = err.error;
          this.toast.error('Comprobante duplicado',
            `La boleta "${d.nro_boleta}" ya fue registrada el ${d.registrado_el} por ${d.registrado_por}.`
          );
        } else {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo registrar el pago'));
        }
      }
    });
  }
}
