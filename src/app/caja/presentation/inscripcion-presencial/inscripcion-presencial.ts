import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { CommonModule, DatePipe } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, FormsModule, Validators } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { CajaService, CajaEstudiante, CajaPrograma, CajaImparticion, CajaPlan, CajaBanco, CajaCuotaPlan } from '../../application/services/caja.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { SettingsService } from '../../../common/application/services/settings.service';
import { EnvioCertificadoService } from '../../../envios-certificado/application/services/envio-certificado.service';
import { DEPARTAMENTOS_BOLIVIA } from '../../../envios-certificado/domain/models/envio-certificado.model';

@Component({
  selector: 'app-inscripcion-presencial',
  standalone: true,
  imports: [CommonModule, DatePipe, ReactiveFormsModule, FormsModule, NgIcon],
  templateUrl: './inscripcion-presencial.html',
})
export default class InscripcionPresencialComponent implements OnInit {
  private fb       = inject(FormBuilder);
  private cajaSvc  = inject(CajaService);
  private settings = inject(SettingsService);
  private envioSvc = inject(EnvioCertificadoService);
  public  authSvc  = inject(AuthService);

  siteLogo   = signal<string | null>(null);
  siteNombre = signal<string>('CENEFCO');

  // ── Sección de Envío a provincia ─────────────────────────────────────────
  readonly DEPARTAMENTOS = DEPARTAMENTOS_BOLIVIA;

  requiereEnvio       = signal(false);
  envioDepartamento   = signal<string>('');
  envioCiudad         = signal<string>('');
  sugerenciasCiudad   = signal<string[]>([]);
  buscandoSugerencias = signal(false);
  mostrarSugerencias  = signal(false);

  /** Valida que si requiereEnvio está activo, ambos campos estén completos */
  envioValido = computed(() =>
    !this.requiereEnvio() ||
    (this.envioDepartamento().trim() !== '' && this.envioCiudad().trim() !== '')
  );

  pasoActual = signal(1); // 1: Estudiante, 2: Programa, 3: Pago, 4: Confirmacion
  isSubmitting = signal(false);

  // Paso 1: Estudiante
  searchCi = signal('');
  isSearchingCi = signal(false);
  estudianteEncontrado = signal<CajaEstudiante | null>(null);
  haBuscado = signal(false); // true solo después de hacer una búsqueda
  
  formEstudiante: FormGroup = this.fb.group({
    id_us: [null],
    nombre: ['', Validators.required],
    apellido_paterno: ['', Validators.required],
    apellido_materno: [''],
    ci: ['', Validators.required],
    expedido: [null],
    celular: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    genero: [2] // 1: Fem, 2: Masc
  });

  // Paso 2: Programa
  searchProg = signal('');
  programas = signal<CajaPrograma[]>([]);
  isSearchingProg = signal(false);
  progSeleccionado = signal<CajaPrograma | null>(null);
  impSeleccionada = signal<CajaImparticion | null>(null);
  planSeleccionado = signal<CajaPlan | null>(null);
  planesDisponibles = computed(() => {
    const imp = this.impSeleccionada();
    if (imp?.planes?.length) return imp.planes;
    return this.progSeleccionado()?.planes ?? [];
  });

  // Paso 3: Pago
  bancos           = signal<CajaBanco[]>([]);
  bancoSeleccionado = signal<CajaBanco | null>(null);
  cuotasPlan       = signal<CajaCuotaPlan[]>([]);   // cuotas template del plan
  cargandoCuotas   = signal(false);
  cuotaSugerida    = signal<CajaCuotaPlan | null>(null); // próxima cuota a pagar
  modoCuotas       = signal<'fecha' | 'dias'>('fecha'); // tipo de fechas del plan
  formPago: FormGroup = this.fb.group({
    monto_pagado: ['', [Validators.required, Validators.min(1)]],
    nro_boleta: [''],
    fecha_deposito: ['', Validators.required],
    metodo_pago: ['deposito', Validators.required],
    tipo_banco_id: [null],
  });

  // Resultado
  resultado = signal<any>(null);

  async descargarQr() {
    const plan = this.planSeleccionado();
    if (!plan || !plan.qr_image_url) return;
    try {
      // Usar ruta relativa para pasar por el proxy de Angular y evitar CORS
      const urlPath = new URL(plan.qr_image_url).pathname;
      const response = await fetch(urlPath);
      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `QR_Pago_${plan.titulo}.png`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
    } catch (err) {
      console.error(err);
      alert('Error al descargar la imagen.');
    }
  }

  async copiarQr() {
    const plan = this.planSeleccionado();
    if (!plan || !plan.qr_image_url) return;
    try {
      // Usar ruta relativa para pasar por el proxy de Angular y evitar CORS
      const urlPath = new URL(plan.qr_image_url).pathname;
      const response = await fetch(urlPath);
      const blob = await response.blob();
      await navigator.clipboard.write([
        new ClipboardItem({
          [blob.type]: blob
        })
      ]);
      alert('¡QR copiado! Ve a WhatsApp Web y presiona Ctrl+V en el chat para pegarlo.');
    } catch (err) {
      console.error(err);
      alert('Tu navegador no permite copiar la imagen automáticamente. Haz clic derecho en el QR y elige Copiar imagen, o descárgala.');
    }
  }

  enviarQrWhatsapp() {
    const plan = this.planSeleccionado();
    if (!plan || !plan.qr_image_url) return;
    
    const banco = this.bancoSeleccionado();
    let text = `Hola! Para completar tu pago de "${plan.titulo}" por Bs. ${plan.costo}, puedes ver el Código QR aquí: 
${plan.qr_image_url}`;
    
    if (banco && banco.numero_cuenta) {
      text += `

O puedes transferir a la cuenta:
Banco: ${banco.nombre}
Cuenta: ${banco.numero_cuenta}
Titular: ${banco.titular || '-'}`;
    }
    
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
  }

  onBancoChange(event: Event) {
    const id = Number((event.target as HTMLSelectElement).value);
    const banco = this.bancos().find(b => b.id === id) ?? null;
    this.bancoSeleccionado.set(banco);
  }

  ngOnInit() {
    this.cajaSvc.getBancos().subscribe(res => this.bancos.set(res));
    this.settings.getSettings().subscribe({
      next: s => {
        if (s.site_logo)  this.siteLogo.set(s.site_logo.startsWith('http') ? s.site_logo : `/storage/${s.site_logo}`);
        if (s.site_name)  this.siteNombre.set(s.site_name);
      },
      error: () => {}
    });
    // Set default date to today
    this.formPago.patchValue({ fecha_deposito: new Date().toISOString().split('T')[0] });
    
    // Make nro_boleta required for all payment methods except efectivo
    this.formPago.get('metodo_pago')!.valueChanges.subscribe(metodo => {
      const boleta = this.formPago.get('nro_boleta')!;
      if (metodo === 'efectivo') {
        boleta.clearValidators();
        boleta.setValue('');
      } else {
        boleta.setValidators([Validators.required]);
      }
      boleta.updateValueAndValidity();
    });
    // Set initial validator based on default method (deposito)
    this.formPago.get('nro_boleta')!.setValidators([Validators.required]);
    this.formPago.get('nro_boleta')!.updateValueAndValidity();
  }

  // --- MÉTODOS PASO 1 ---
  buscarCi() {
    const ci = this.searchCi().trim();
    if (ci.length < 3) return;
    this.isSearchingCi.set(true);
    this.cajaSvc.buscarEstudiante(ci).subscribe({
      next: (est) => {
        this.estudianteEncontrado.set(est);
        this.haBuscado.set(true);
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
    this.haBuscado.set(false);
    this.formEstudiante.reset({ genero: 2 });
    this.searchCi.set('');
  }

  irPaso2() {
    if (this.formEstudiante.valid) {
      // Validar sección de envío antes de avanzar
      if (this.requiereEnvio() && !this.envioValido()) {
        Swal.fire('Atención', 'Si marcas envío a provincia, debes seleccionar el departamento y escribir la ciudad/provincia de destino.', 'warning');
        return;
      }
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
    this.cuotasPlan.set([]);
    this.cuotaSugerida.set(null);
    // Cargar las cuotas individuales del plan
    this.cargandoCuotas.set(true);
    this.cajaSvc.getCuotasPlan(plan.id_plan).subscribe({
      next: (res) => {
        const cuotas = res.data.sort((a, b) =>
          Number(a.nro_pago ?? 0) - Number(b.nro_pago ?? 0)
        );
        this.cuotasPlan.set(cuotas);
        this.cargandoCuotas.set(false);
        // Detectar modo: si alguna cuota tiene dias_desde_inscripcion != null → modo días
        this.modoCuotas.set(cuotas.some(c => c.dias_desde_inscripcion != null) ? 'dias' : 'fecha');
        // Si hay cuotas configuradas, sugerir la primera
        if (cuotas.length > 0) {
          this.cuotaSugerida.set(cuotas[0]);
          this.formPago.patchValue({ monto_pagado: cuotas[0].monto_a_pagar });
        } else {
          // Fallback: usar el costo total si no hay cuotas definidas
          this.formPago.patchValue({ monto_pagado: plan.costo });
        }
      },
      error: () => {
        this.cargandoCuotas.set(false);
        // Fallback si falla la carga de cuotas
        this.formPago.patchValue({ monto_pagado: plan.costo });
      },
    });
  }

  irPaso3() {
    if (!this.impSeleccionada()) {
      Swal.fire('Atención', 'Debes seleccionar una versión para continuar.', 'warning');
      return;
    }
    const hayPlanes = this.planesDisponibles().length > 0;
    if (hayPlanes && !this.planSeleccionado()) {
      Swal.fire('Atención', 'Debes seleccionar un plan de pago para continuar.', 'warning');
      return;
    }

    const cuotas = this.cuotasPlan();

    // En Caja/Inscripción siempre se está creando una inscripción NUEVA para
    // este programa/versión. Por lo tanto la primera cuota a cobrar es SIEMPRE
    // la cuota 1 del plan — independientemente de si el estudiante tiene
    // inscripciones previas en otros diplomados con el mismo plan.
    // NO se debe consultar getCuotasPendientes aquí porque mezclaría cuotas
    // de otras inscripciones del mismo plan en programas distintos.
    if (cuotas.length > 0) {
      this.cuotaSugerida.set(cuotas[0]);
      this.formPago.patchValue({ monto_pagado: cuotas[0].monto_a_pagar });
    }

    this.pasoActual.set(3);
  }

  // ── Autocomplete de ciudades ─────────────────────────────────────────────
  comprobanteFile = signal<File | null>(null);

  onFileChange(event: any) {
    const file = event.target.files[0];
    if (file) { this.comprobanteFile.set(file); } else { this.comprobanteFile.set(null); }
  }

  onCiudadInput(event: Event): void {
    const val = (event.target as HTMLInputElement).value;
    this.envioCiudad.set(val);
    if (val.length < 2) { this.sugerenciasCiudad.set([]); this.mostrarSugerencias.set(false); return; }
    this.buscandoSugerencias.set(true);
    this.envioSvc.autocomplete(val, this.envioDepartamento() || undefined).subscribe({
      next: s => { this.sugerenciasCiudad.set(s); this.mostrarSugerencias.set(s.length > 0); this.buscandoSugerencias.set(false); },
      error: () => this.buscandoSugerencias.set(false),
    });
  }

  seleccionarCiudad(ciudad: string): void {
    this.envioCiudad.set(ciudad);
    this.mostrarSugerencias.set(false);
  }

  // ── Finalizar inscripción ─────────────────────────────────────────────────
  finalizarInscripcion() {
    if (this.formPago.invalid) {
      this.formPago.markAllAsTouched();
      Swal.fire('Formulario Incompleto', 'Por favor revisa los campos marcados en rojo (por ejemplo, te falta el Nro. de Boleta / Recibo).', 'warning');
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

        // Si requiere envío, crear el registro de envío con el id_ins recién generado
        if (this.requiereEnvio() && res.id_ins && this.envioDepartamento() && this.envioCiudad()) {
          this.envioSvc.create({
            id_ins:         res.id_ins,
            departamento:   this.envioDepartamento(),
            ciudad_destino: this.envioCiudad(),
            fecha_envio:    new Date().toISOString().split('T')[0],
          }).subscribe({ error: () => {} }); // silencioso — no bloquea el flujo principal
        }

        this.pasoActual.set(4);
      },
      error: (err) => {
        this.isSubmitting.set(false);
        let errorMsg = err.error?.message || 'Error al inscribir';
        if (err.error?.errors) {
          const firstKey = Object.keys(err.error.errors)[0];
          errorMsg = err.error.errors[firstKey][0];
        }
        Swal.fire('Error', errorMsg, 'error');
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
    this.cuotasPlan.set([]);
    this.cuotaSugerida.set(null);
    this.modoCuotas.set('fecha');
    this.requiereEnvio.set(false);
    this.envioDepartamento.set('');
    this.envioCiudad.set('');
    this.sugerenciasCiudad.set([]);
    this.formPago.reset({
      metodo_pago: 'deposito',
      fecha_deposito: new Date().toISOString().split('T')[0]
    });
  }
}