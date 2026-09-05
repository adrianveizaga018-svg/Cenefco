import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { DecimalPipe, SlicePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { NgIcon } from '@ng-icons/core';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { CompromisoCobroService } from '../../application/services/compromiso-cobro.service';
import {
  CompromisoCobro,
  EstadoCompromisoCobro,
  ESTADO_COMPROMISO_CLASES,
  ESTADO_COMPROMISO_LABELS,
  MOTIVOS_REPROGRAMACION,
  MotivoReprogramacion,
  ResumenCompromisosCobro,
} from '../../domain/models/compromiso-cobro.model';

@Component({
  selector: 'app-compromisos-cobro',
  imports: [NgIcon, PageTitle, Pagination, RouterLink, FormsModule, DecimalPipe, SlicePipe],
  templateUrl: './compromisos-cobro.html',
})
export class CompromisosCobro implements OnInit {
  private service = inject(CompromisoCobroService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  readonly hoyIsoAttr = new Date().toISOString().split('T')[0];
  readonly motivosReprogramacion = MOTIVOS_REPROGRAMACION;

  readonly filtros: { value: '' | EstadoCompromisoCobro; label: string }[] = [
    { value: '',           label: 'Todos' },
    { value: 'pendiente',  label: 'Pendientes' },
    { value: 'incumplido', label: 'Incumplidos' },
    { value: 'cumplido',   label: 'Cumplidos' },
    { value: 'cancelado',  label: 'Cancelados' },
  ];

  items    = signal<CompromisoCobro[]>([]);
  loading  = signal(true);

  currentPage = signal(1);
  pageSize    = 15;
  total       = signal(0);
  query       = signal('');
  estadoFiltro = signal<'' | EstadoCompromisoCobro>('pendiente');

  resumen = signal<ResumenCompromisosCobro | null>(null);

  procesandoId = signal<number | null>(null);

  ngOnInit(): void {
    this.cargarResumen();
    this.load();
    this.pedirPermisoNotificacionEscritorio();
  }

  private pedirPermisoNotificacionEscritorio(): void {
    if (typeof Notification === 'undefined' || Notification.permission !== 'default') return;
    Notification.requestPermission();
  }

  private cargarResumen(): void {
    this.service.getResumen().subscribe({
      next: r => { this.resumen.set(r); this.cdr.detectChanges(); },
      error: () => {},
    });
  }

  load(): void {
    this.loading.set(true);
    this.service.getAll({
      pageIndex: this.currentPage(),
      pageSize:  this.pageSize,
      query:     this.query(),
      estado:    this.estadoFiltro() || undefined,
    }).subscribe({
      next: res => {
        this.items.set(res.data);
        this.total.set(res.total);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de compromisos de cobro.');
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  onPageChange(page: number): void {
    this.currentPage.set(page);
    this.load();
  }

  onBuscar(valor: string): void {
    this.query.set(valor);
    this.currentPage.set(1);
    this.load();
  }

  onFiltrarEstado(estado: '' | EstadoCompromisoCobro): void {
    this.estadoFiltro.set(estado);
    this.currentPage.set(1);
    this.load();
  }

  estadoLabel(estado: EstadoCompromisoCobro): string {
    return ESTADO_COMPROMISO_LABELS[estado] ?? estado;
  }

  estadoClase(estado: EstadoCompromisoCobro): string {
    return ESTADO_COMPROMISO_CLASES[estado] ?? 'bg-default-100 text-default-500';
  }

  esVencido(item: CompromisoCobro): boolean {
    return (item.estado === 'pendiente' || item.estado === 'incumplido')
      && item.fecha_compromiso < this.hoyIsoAttr;
  }

  marcarCumplido(item: CompromisoCobro): void {
    Swal.fire({
      title: '¿Marcar como cumplido?',
      text: 'Usa esta opción si el estudiante ya pagó y el pago se registrará más tarde.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, marcar cumplido',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.procesandoId.set(item.id);
      this.service.cumplir(item.id).subscribe({
        next: () => {
          this.toast.success('Cumplido', 'El compromiso fue marcado como cumplido');
          this.procesandoId.set(null);
          this.load();
          this.cargarResumen();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar el compromiso'));
          this.procesandoId.set(null);
        },
      });
    });
  }

  reprogramar(item: CompromisoCobro): void {
    const opciones = this.motivosReprogramacion
      .map(m => `<option value="${m.value}">${m.label}</option>`)
      .join('');

    Swal.fire({
      title: 'Reprogramar compromiso',
      html: `
        <div style="text-align:left;">
          <div style="display:flex; gap:8px;">
            <div style="flex:1;">
              <label style="font-size:12px;font-weight:600;color:#475569;">Nueva fecha</label>
              <input id="swal-cc-fecha" type="date" class="swal2-input" min="${this.hoyIsoAttr}" style="margin:4px 0 12px;">
            </div>
            <div style="flex:1;">
              <label style="font-size:12px;font-weight:600;color:#475569;">Hora (opcional)</label>
              <input id="swal-cc-hora" type="time" class="swal2-input" style="margin:4px 0 12px;">
            </div>
          </div>
          <label style="font-size:12px;font-weight:600;color:#475569;">Motivo</label>
          <select id="swal-cc-motivo" class="swal2-input" style="margin:4px 0 12px;">${opciones}</select>
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#0d9488',
      cancelButtonColor: '#6b7280',
      preConfirm: () => {
        const fecha  = (document.getElementById('swal-cc-fecha') as HTMLInputElement)?.value;
        const hora   = (document.getElementById('swal-cc-hora') as HTMLInputElement)?.value;
        const motivo = (document.getElementById('swal-cc-motivo') as HTMLSelectElement)?.value as MotivoReprogramacion;
        if (!fecha) {
          Swal.showValidationMessage('Seleccione la nueva fecha');
          return false;
        }
        return { fecha, hora, motivo };
      },
    }).then(r => {
      if (!r.isConfirmed || !r.value) return;
      this.procesandoId.set(item.id);
      this.service.reprogramar(item.id, {
        nueva_fecha: r.value.fecha,
        nueva_hora: r.value.hora || null,
        motivo: r.value.motivo,
      }).subscribe({
        next: () => {
          this.toast.success('Reprogramado', 'El compromiso fue reprogramado');
          this.procesandoId.set(null);
          this.load();
          this.cargarResumen();
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo reprogramar el compromiso'));
          this.procesandoId.set(null);
        },
      });
    });
  }
}
