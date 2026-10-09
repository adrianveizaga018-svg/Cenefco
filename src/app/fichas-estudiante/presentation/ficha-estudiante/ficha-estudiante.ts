import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { DecimalPipe, DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { CajaService } from '../../../caja/application/services/caja.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { FichaEstudianteService } from '../../application/services/ficha-estudiante.service';
import { EmisionCertificadoService } from '../../application/services/emision-certificado.service';
import { CertificadoFicha, EstudianteResumen, FichaEstudiante, InscripcionFicha } from '../../domain/models/ficha-estudiante.model';

/**
 * Ficha del estudiante para Caja y personal académico: buscar por carnet o nombre
 * y ver en un solo lugar sus programas, pagos, acceso a la plataforma y certificados.
 */
@Component({
  selector: 'app-ficha-estudiante',
  standalone: true,
  imports: [PageTitle, NgIcon, RouterLink, FormsModule, DecimalPipe, DatePipe],
  templateUrl: './ficha-estudiante.html',
})
export class FichaEstudianteComponent implements OnInit {
  private fichas   = inject(FichaEstudianteService);
  private emision  = inject(EmisionCertificadoService);
  private cajaSvc  = inject(CajaService);
  private toast    = inject(ToastService);
  private auth     = inject(AuthService);
  private route    = inject(ActivatedRoute);
  private router   = inject(Router);

  busqueda    = '';
  buscando    = signal(false);
  haBuscado   = signal(false);
  resultados  = signal<EstudianteResumen[]>([]);

  ficha       = signal<FichaEstudiante | null>(null);
  cargando    = signal(false);
  error       = signal('');
  enviandoCredenciales = signal(false);

  readonly puedeEmitir      = this.auth.hasPermission('certificados.crear');
  readonly puedeCredenciales = this.auth.hasPermission('inscripciones.crear');

  nombreCompleto = computed(() => {
    const e = this.ficha()?.estudiante;
    return e ? [e.nombre, e.appaterno, e.apmaterno].filter(Boolean).join(' ') : '';
  });

  totales = computed(() => {
    const ins = this.ficha()?.inscripciones ?? [];
    return {
      programas: ins.length,
      activos:   ins.filter(i => i.activa).length,
      deuda:     ins.filter(i => i.activa).reduce((s, i) => s + Math.max(i.pendiente ?? 0, 0), 0),
    };
  });

  ngOnInit(): void {
    this.route.paramMap.subscribe(params => {
      const idUs = Number(params.get('idUs'));
      if (idUs) {
        this.cargarFicha(idUs);
      } else {
        this.ficha.set(null);
      }
    });
  }

  buscar(): void {
    const texto = this.busqueda.trim();
    if (texto.length < 3) {
      this.toast.warning('Búsqueda', 'Escribe al menos 3 caracteres (carnet o nombre).');
      return;
    }
    this.buscando.set(true);
    this.fichas.buscar(texto).subscribe({
      next: (res) => {
        this.buscando.set(false);
        this.haBuscado.set(true);
        this.resultados.set(res);
        // Un solo resultado (p. ej. carnet exacto): abrir directo su ficha
        if (res.length === 1) this.abrir(res[0]);
      },
      error: (err: HttpErrorResponse) => {
        this.buscando.set(false);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo buscar.'));
      },
    });
  }

  abrir(est: EstudianteResumen): void {
    this.router.navigate(['/cenefco/estudiantes', est.id_us]);
  }

  nombreDe(est: EstudianteResumen): string {
    return [est.nombre, est.appaterno, est.apmaterno].filter(Boolean).join(' ');
  }

  private cargarFicha(idUs: number): void {
    this.cargando.set(true);
    this.error.set('');
    this.fichas.getFicha(idUs).subscribe({
      next: (f) => { this.ficha.set(f); this.cargando.set(false); },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.ficha.set(null);
        this.error.set(extractErrorMessage(err, 'No se pudo cargar la ficha del estudiante.'));
      },
    });
  }

  async emitirCertificado(ins: InscripcionFicha): Promise<void> {
    const emitido = await this.emision.emitir(ins.id_ins, this.nombreCompleto());
    if (emitido) this.cargarFicha(this.ficha()!.estudiante.id_us);
  }

  enviarCredenciales(): void {
    const f = this.ficha();
    if (!f?.estudiante.email || this.enviandoCredenciales()) return;

    Swal.fire({
      title: '¿Enviar credenciales de acceso?',
      html: `<p class="text-sm text-gray-700">Se enviará a <strong>${f.estudiante.email.replace(/[<>&"']/g, '')}</strong> un enlace para crear o cambiar su contraseña.</p>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, enviar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#0B4258',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.enviandoCredenciales.set(true);
      this.cajaSvc.enviarCredenciales(f.estudiante.id_us).subscribe({
        next: (res) => {
          this.enviandoCredenciales.set(false);
          this.toast.success('Credenciales enviadas', `Correo enviado a ${res.email}.`);
          this.cargarFicha(f.estudiante.id_us);
        },
        error: (err: HttpErrorResponse) => {
          this.enviandoCredenciales.set(false);
          this.toast.error('No se enviaron', extractErrorMessage(err, 'No se pudieron enviar las credenciales.'));
        },
      });
    });
  }

  abrirCertificado(cert: CertificadoFicha): void {
    if (cert.archivo_url) window.open(cert.archivo_url, '_blank', 'noopener');
  }

  /** Abre el certificado en una ventana y lanza el diálogo de impresión. */
  imprimirCertificado(cert: CertificadoFicha): void {
    if (!cert.archivo_url) return;
    if (cert.archivo_url.toLowerCase().endsWith('.pdf')) {
      // El visor de PDF del navegador ya trae su propio botón de imprimir
      window.open(cert.archivo_url, '_blank', 'noopener');
      return;
    }
    const w = window.open('', '_blank');
    if (!w) return;
    w.document.write(`<!doctype html><html><head><title>Certificado ${cert.codigo_verificacion}</title>
      <style>@page{size:landscape;margin:0}html,body{margin:0}img{width:100%;height:auto;display:block}</style></head>
      <body><img src="${cert.archivo_url}" onload="window.focus();window.print()"></body></html>`);
    w.document.close();
  }

  etiquetaPago(ins: InscripcionFicha): { texto: string; clase: string } {
    if (!ins.activa) return { texto: 'Dado de baja', clase: 'bg-default-100 text-default-500' };
    if (ins.plan_no_asignado) return { texto: 'Sin plan de pago', clase: 'bg-default-100 text-default-600' };
    if (ins.pago_completo) return { texto: 'Pagado', clase: 'bg-success/10 text-success' };
    return { texto: `Debe Bs. ${(ins.pendiente ?? 0).toLocaleString('es-BO', { minimumFractionDigits: 2 })}`, clase: 'bg-warning/10 text-warning' };
  }

  tieneCertificado(ins: InscripcionFicha): CertificadoFicha | undefined {
    const f = this.ficha();
    return f?.certificados.find(c => Number(c.imparte_id) === Number(ins.id_imp) && c.estado !== 'anulado');
  }
}
