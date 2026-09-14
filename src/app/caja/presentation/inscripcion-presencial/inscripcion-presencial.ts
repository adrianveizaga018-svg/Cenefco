import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, FormsModule, Validators } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { CajaService, CajaEstudiante, CajaPrograma, CajaImparticion, CajaPlan, CajaBanco } from '../../application/services/caja.service';

@Component({
  selector: 'app-inscripcion-presencial',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, NgIcon],
  templateUrl: './inscripcion-presencial.html',
})
export default class InscripcionPresencialComponent implements OnInit {
  private fb = inject(FormBuilder);
  private cajaSvc = inject(CajaService);

  pasoActual = signal(1); // 1: Estudiante, 2: Programa, 3: Pago, 4: Confirmacion
  isSubmitting = signal(false);

  // Paso 1: Estudiante
  searchCi = signal('');
  isSearchingCi = signal(false);
  estudianteEncontrado = signal<CajaEstudiante | null>(null);
  
  formEstudiante: FormGroup = this.fb.group({
    id_us: [null],
    nombre: ['', Validators.required],
    apellido_paterno: [''],
    apellido_materno: [''],
    ci: ['', Validators.required],
    expedido: [null],
    celular: [''],
    email: ['', [Validators.email]],
    genero: [2] // 1: Fem, 2: Masc
  });

  // Paso 2: Programa
  searchProg = signal('');
  programas = signal<CajaPrograma[]>([]);
  isSearchingProg = signal(false);
  progSeleccionado = signal<CajaPrograma | null>(null);
  impSeleccionada = signal<CajaImparticion | null>(null);
  planSeleccionado = signal<CajaPlan | null>(null);

  // Paso 3: Pago
  bancos = signal<CajaBanco[]>([]);
  formPago: FormGroup = this.fb.group({
    monto_pagado: ['', [Validators.required, Validators.min(1)]],
    nro_boleta: ['', Validators.required],
    fecha_deposito: ['', Validators.required],
    metodo_pago: ['deposito', Validators.required],
    tipo_banco_id: [null],
  });

  // Resultado
  resultado = signal<any>(null);

  ngOnInit() {
    this.cajaSvc.getBancos().subscribe(res => this.bancos.set(res));
    // Set default date to today
    this.formPago.patchValue({ fecha_deposito: new Date().toISOString().split('T')[0] });
  }

  // --- MÉTODOS PASO 1 ---
  buscarCi() {
    const ci = this.searchCi().trim();
    if (ci.length < 3) return;
    this.isSearchingCi.set(true);
    this.cajaSvc.buscarEstudiante(ci).subscribe({
      next: (est) => {
        this.estudianteEncontrado.set(est);
        if (est) {
          this.formEstudiante.patchValue({
            id_us: est.id_us,
            nombre: est.nombre,
            apellido_paterno: est.apellido_paterno,
            apellido_materno: est.apellido_materno,
            ci: est.ci,
            expedido: est.expedido,
            celular: est.celular,
            email: est.email,
            genero: est.genero || 2
          });
        } else {
          this.formEstudiante.reset({ ci, genero: 2 });
        }
        this.isSearchingCi.set(false);
      },
      error: () => this.isSearchingCi.set(false)
    });
  }

  nuevoEstudiante() {
    this.estudianteEncontrado.set(null);
    this.formEstudiante.reset({ genero: 2 });
    this.searchCi.set('');
  }

  irPaso2() {
    if (this.formEstudiante.valid) {
      const est = this.estudianteEncontrado();
      if (est && this.formEstudiante.dirty) {
        Swal.fire({
          title: '¿Actualizar datos?',
          text: 'Modificaste los datos de este estudiante. ¿Deseas guardar estos cambios en su registro?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, actualizar y continuar',
          cancelButtonText: 'No, solo continuar',
        }).then((result) => {
          // Guardamos la decisión para mandarla al final en el payload
          (this.formEstudiante as any).actualizar_estudiante = result.isConfirmed;
          this.avanzarPaso2();
        });
      } else {
        (this.formEstudiante as any).actualizar_estudiante = false;
        this.avanzarPaso2();
      }
    } else {
      this.formEstudiante.markAllAsTouched();
    }
  }

  private avanzarPaso2() {
    this.pasoActual.set(2);
    if (this.programas().length === 0) {
      this.buscarProgramas();
    }
  }

  // --- MÉTODOS PASO 2 ---
  buscarProgramas() {
    this.isSearchingProg.set(true);
    this.cajaSvc.buscarProgramas(this.searchProg()).subscribe({
      next: (res) => {
        this.programas.set(res);
        this.isSearchingProg.set(false);
      },
      error: () => this.isSearchingProg.set(false)
    });
  }

  seleccionarPrograma(prog: CajaPrograma) {
    this.progSeleccionado.set(prog);
    this.impSeleccionada.set(null);
    this.planSeleccionado.set(null);
  }

  seleccionarImparticion(imp: CajaImparticion) {
    this.impSeleccionada.set(imp);
    this.planSeleccionado.set(null);
  }

  seleccionarPlan(plan: CajaPlan) {
    this.planSeleccionado.set(plan);
    this.formPago.patchValue({ monto_pagado: plan.costo });
  }

  irPaso3() {
    if (!this.impSeleccionada()) {
      Swal.fire('Atención', 'Debes seleccionar una versión para continuar.', 'warning');
      return;
    }
    const hayPlanes = (this.progSeleccionado()?.planes?.length ?? 0) > 0;
    if (hayPlanes && !this.planSeleccionado()) {
      Swal.fire('Atención', 'Debes seleccionar un plan de pago para continuar.', 'warning');
      return;
    }
    this.pasoActual.set(3);
  }

  // --- MÉTODOS PASO 3 ---
  comprobanteFile = signal<File | null>(null);

  onFileChange(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.comprobanteFile.set(file);
    } else {
      this.comprobanteFile.set(null);
    }
  }

  finalizarInscripcion() {
    if (this.formPago.invalid) {
      this.formPago.markAllAsTouched();
      return;
    }

    const payload = {
      ...this.formEstudiante.value,
      ...this.formPago.value,
      actualizar_estudiante: !!(this.formEstudiante as any).actualizar_estudiante,
      id_imp: this.impSeleccionada()?.id_imp,
      id_plan: this.planSeleccionado()?.id_plan,
    };

    this.isSubmitting.set(true);
    this.cajaSvc.inscribir(payload, this.comprobanteFile()).subscribe({
      next: (res) => {
        this.resultado.set(res);
        this.isSubmitting.set(false);
        this.pasoActual.set(4);
      },
      error: (err) => {
        this.isSubmitting.set(false);
        Swal.fire('Error', err.error?.message || 'Error al inscribir', 'error');
      }
    });
  }

  // --- MÉTODOS PASO 4 ---
  imprimirComprobante() {
    window.print();
  }

  nuevaInscripcion() {
    this.pasoActual.set(1);
    this.nuevoEstudiante();
    this.progSeleccionado.set(null);
    this.impSeleccionada.set(null);
    this.planSeleccionado.set(null);
    this.formPago.reset({
      metodo_pago: 'deposito',
      fecha_deposito: new Date().toISOString().split('T')[0]
    });
  }
}