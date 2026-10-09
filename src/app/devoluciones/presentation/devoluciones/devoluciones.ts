import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { DevolucionPanelService } from '../../application/services/devolucion-panel.service';
import { DevolucionPanel, EstadoDevolucion } from '../../domain/models/devolucion.model';

/**
 * Panel de devoluciones: saldo a favor por cambio de programa u otras devoluciones
 * registradas en una inscripción. Aquí se aprueban o rechazan.
 */
@Component({
  selector: 'app-devoluciones',
  standalone: true,
  imports: [PageTitle, NgIcon, RouterLink, DecimalPipe, DatePipe],
  templateUrl: './devoluciones.html',
})
export class DevolucionesComponent implements OnInit {
  private svc   = inject(DevolucionPanelService);
  private toast = inject(ToastService);
  private auth  = inject(AuthService);

  readonly filtros: { valor: EstadoDevolucion | 'todas'; etiqueta: string }[] = [
    { valor: 'pendiente', etiqueta: 'Pendientes' },
    { valor: 'aprobada',  etiqueta: 'Aprobadas' },
    { valor: 'rechazada', etiqueta: 'Rechazadas' },
    { valor: 'todas',     etiqueta: 'Todas' },
  ];

  filtro         = signal<EstadoDevolucion | 'todas'>('pendiente');
  devoluciones   = signal<DevolucionPanel[]>([]);
  pendientes     = signal(0);
  montoPendiente = signal(0);
  cargando       = signal(true);
  procesandoId   = signal<number | null>(null);

  readonly puedeResolver = this.auth.hasPermission('devoluciones.aprobar');

  ngOnInit(): void {
    this.cargar();
  }

  cambiarFiltro(valor: EstadoDevolucion | 'todas'): void {
    this.filtro.set(valor);
    this.cargar();
  }

  cargar(): void {
    this.cargando.set(true);
    this.svc.listar(this.filtro()).subscribe({
      next: (res) => {
        this.devoluciones.set(res.data);
        this.pendientes.set(res.pendientes);
        this.montoPendiente.set(res.monto_pendiente);
        this.cargando.set(false);
      },
      error: (err: HttpErrorResponse) => {
        this.cargando.set(false);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudieron cargar las devoluciones.'));
      },
    });
  }

  async resolver(dev: DevolucionPanel, estado: 'aprobada' | 'rechazada'): Promise<void> {
    const aprobar = estado === 'aprobada';
    const r = await Swal.fire({
      icon: aprobar ? 'question' : 'warning',
      title: aprobar ? '¿Aprobar devolución?' : '¿Rechazar devolución?',
      html: `<p class="text-sm text-gray-700">${aprobar ? 'Se devolverán' : 'No se devolverán'} <strong>Bs. ${dev.monto.toFixed(2)}</strong>
             a <strong>${(dev.estudiante ?? 'el estudiante').replace(/[<>&"']/g, '')}</strong>.</p>`,
      input: 'text',
      inputLabel: aprobar ? 'Nota (opcional): cómo y cuándo se devuelve' : 'Motivo del rechazo',
      inputPlaceholder: aprobar ? 'Ej: transferencia bancaria el 10/10' : 'Ej: el estudiante pidió mantenerlo como crédito',
      inputValidator: (v) => (!aprobar && !v?.trim() ? 'Escribe el motivo del rechazo.' : null),
      showCancelButton: true,
      confirmButtonText: aprobar ? 'Aprobar devolución' : 'Rechazar devolución',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: aprobar ? '#16a34a' : '#dc2626',
    });
    if (!r.isConfirmed) return;

    this.procesandoId.set(dev.id);
    this.svc.resolver(dev.id, estado, (r.value as string)?.trim() || null).subscribe({
      next: () => {
        this.procesandoId.set(null);
        this.toast.success(aprobar ? 'Devolución aprobada' : 'Devolución rechazada', `Bs. ${dev.monto.toFixed(2)} — ${dev.estudiante ?? ''}`);
        this.cargar();
      },
      error: (err: HttpErrorResponse) => {
        this.procesandoId.set(null);
        this.toast.error('No se pudo resolver', extractErrorMessage(err, 'Inténtalo de nuevo.'));
      },
    });
  }

  claseEstado(estado: EstadoDevolucion): string {
    return {
      pendiente: 'bg-amber-100 text-amber-700',
      aprobada:  'bg-success/10 text-success',
      rechazada: 'bg-danger/10 text-danger',
      cancelada: 'bg-default-100 text-default-500',
    }[estado];
  }
}
