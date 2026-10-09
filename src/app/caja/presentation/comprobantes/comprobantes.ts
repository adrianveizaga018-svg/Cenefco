import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { ComprobanteEstudiante, ComprobantesCajaService, EstadoComprobante } from '../../application/services/comprobantes-caja.service';

/**
 * Comprobantes que los estudiantes suben desde su panel (pago de cuota o inscripción online).
 * No descuentan deuda hasta que Caja los aprueba.
 */
@Component({
  selector: 'app-comprobantes-caja',
  standalone: true,
  imports: [PageTitle, NgIcon, RouterLink, DecimalPipe, DatePipe],
  templateUrl: './comprobantes.html',
})
export class ComprobantesCajaComponent implements OnInit {
  private svc   = inject(ComprobantesCajaService);
  private toast = inject(ToastService);

  readonly filtros: { valor: EstadoComprobante; etiqueta: string }[] = [
    { valor: 'por_revisar', etiqueta: 'Por revisar' },
    { valor: 'aprobados',   etiqueta: 'Aprobados' },
    { valor: 'rechazados',  etiqueta: 'Rechazados' },
  ];

  filtro       = signal<EstadoComprobante>('por_revisar');
  comprobantes = signal<ComprobanteEstudiante[]>([]);
  pendientes   = signal(0);
  cargando     = signal(true);
  procesandoId = signal<number | null>(null);

  ngOnInit(): void {
    this.cargar();
  }

  cambiarFiltro(valor: EstadoComprobante): void {
    this.filtro.set(valor);
    this.cargar();
  }

  cargar(): void {
    this.cargando.set(true);
    this.svc.listar(this.filtro()).subscribe({
      next: (res) => { this.comprobantes.set(res.data); this.pendientes.set(res.pendientes); this.cargando.set(false); },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudieron cargar los comprobantes.'));
      },
    });
  }

  esPdf(url: string | null): boolean {
    return !!url && url.toLowerCase().endsWith('.pdf');
  }

  async aprobar(c: ComprobanteEstudiante): Promise<void> {
    const r = await Swal.fire({
      icon: 'question',
      title: '¿Aprobar este pago?',
      html: `<p class="text-sm text-gray-700">Se registrará el pago de <strong>Bs. ${c.monto.toFixed(2)}</strong> de
             <strong>${this.escapar(c.estudiante)}</strong> y se descontará de su saldo.</p>
             <p class="text-xs text-gray-500 mt-2">Revisa antes que el dinero haya llegado a la cuenta.</p>`,
      showCancelButton: true,
      confirmButtonText: 'Sí, aprobar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#16a34a',
    });
    if (!r.isConfirmed) return;

    this.procesandoId.set(c.id_pago);
    this.svc.aprobar(c.id_pago).subscribe({
      next: (res) => {
        this.procesandoId.set(null);
        this.toast.success('Pago aprobado', res.saldo > 0 ? `Le quedan Bs. ${res.saldo.toFixed(2)} por pagar.` : 'El estudiante quedó al día.');
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.procesandoId.set(null);
        this.toast.error('No se pudo aprobar', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  async rechazar(c: ComprobanteEstudiante): Promise<void> {
    const r = await Swal.fire({
      icon: 'warning',
      title: '¿Rechazar este comprobante?',
      html: `<p class="text-sm text-gray-700">Le enviaremos el motivo a <strong>${this.escapar(c.estudiante)}</strong> para que suba otro.</p>`
        + (c.es_inscripcion ? `<p class="text-xs text-amber-700 mt-2">Es una inscripción nueva: si no va a pagar, dala de baja desde su inscripción.</p>` : ''),
      input: 'text',
      inputLabel: 'Motivo',
      inputPlaceholder: 'Ej: la foto está borrosa / el monto no llegó a la cuenta',
      inputValidator: (v) => (!v || v.trim().length < 5 ? 'Escribe el motivo (mínimo 5 letras).' : null),
      showCancelButton: true,
      confirmButtonText: 'Rechazar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc2626',
    });
    if (!r.isConfirmed) return;

    this.procesandoId.set(c.id_pago);
    this.svc.rechazar(c.id_pago, (r.value as string).trim()).subscribe({
      next: () => {
        this.procesandoId.set(null);
        this.toast.success('Comprobante rechazado', 'Le avisamos al estudiante con el motivo.');
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.procesandoId.set(null);
        this.toast.error('No se pudo rechazar', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  private escapar(texto: string): string {
    return (texto ?? '').replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]!));
  }
}
