import { Component, inject, signal, OnInit } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { DatePipe } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EnvioCertificadoService } from '../../application/services/envio-certificado.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { EnvioCertificado, EstadoEnvio, DEPARTAMENTOS_BOLIVIA } from '../../domain/models/envio-certificado.model';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-dashboard-envios',
  standalone: true,
  imports: [ReactiveFormsModule, NgIcon, PageTitle, DatePipe],
  templateUrl: './dashboard-envios.html',
})
export class DashboardEnvios implements OnInit {
  private service = inject(EnvioCertificadoService);
  private toast   = inject(ToastService);

  readonly DEPARTAMENTOS = DEPARTAMENTOS_BOLIVIA;

  envios         = signal<EnvioCertificado[]>([]);
  cargando       = signal(false);
  totalRegistros = signal(0);
  paginaActual   = signal(1);

  // Modal para marcar enviado
  enviandoId     = signal<number | null>(null);
  procesando     = signal(false);

  formMarcar = new FormGroup({
    agencia:         new FormControl(''),
    nro_seguimiento: new FormControl(''),
    fecha_entrega:   new FormControl(''),
    aclaraciones:    new FormControl(''),
  });

  filtros = new FormGroup({
    estado:       new FormControl('pendiente'),
    departamento: new FormControl(''),
    ci:           new FormControl(''),
    fecha_desde:  new FormControl(''),
    fecha_hasta:  new FormControl(''),
  });

  // Resumen rápido
  resumen = signal<{ pendientes: number; enviados: number; entregados: number }>({
    pendientes: 0, enviados: 0, entregados: 0
  });

  ngOnInit() {
    this.cargar();
  }

  cargar(page = 1) {
    this.cargando.set(true);
    this.paginaActual.set(page);
    const f = this.filtros.value;
    this.service.getDashboard({
      estado:       f.estado       || undefined,
      departamento: f.departamento || undefined,
      ci:           f.ci           || undefined,
      fecha_desde:  f.fecha_desde  || undefined,
      fecha_hasta:  f.fecha_hasta  || undefined,
      page,
      per_page: 25,
    }).subscribe({
      next: res => {
        this.envios.set(res.data);
        this.totalRegistros.set(res.total);
        this.cargando.set(false);
        this.calcularResumen(res.data);
      },
      error: () => this.cargando.set(false),
    });
  }

  private calcularResumen(data: EnvioCertificado[]) {
    // Solo actualiza si no hay filtro de estado (para mostrar todos los estados)
    if (!this.filtros.value.estado) {
      this.resumen.set({
        pendientes: data.filter(e => e.estado === 'pendiente').length,
        enviados:   data.filter(e => e.estado === 'enviado').length,
        entregados: data.filter(e => e.estado === 'entregado').length,
      });
    }
  }

  aplicarFiltros() { this.cargar(1); }
  limpiarFiltros() { this.filtros.reset({ estado: '' }); this.cargar(1); }
  cambiarPagina(delta: number) {
    const np = this.paginaActual() + delta;
    if (np > 0) this.cargar(np);
  }

  // ── Modal para marcar como enviado ─────────────────────────────────────
  abrirModalEnviar(id: number) {
    this.enviandoId.set(id);
    this.formMarcar.reset();
    this.formMarcar.patchValue({ fecha_entrega: new Date().toISOString().split('T')[0] });
  }

  cerrarModal() { this.enviandoId.set(null); }

  confirmarEnviado() {
    const id = this.enviandoId();
    if (!id) return;
    this.procesando.set(true);
    this.service.update(id, {
      estado:          'enviado',
      agencia:         this.formMarcar.value.agencia         || null,
      nro_seguimiento: this.formMarcar.value.nro_seguimiento || null,
      aclaraciones:    this.formMarcar.value.aclaraciones    || null,
    }).subscribe({
      next: () => {
        this.toast.success('¡Marcado!', 'El envío fue marcado como enviado.');
        this.procesando.set(false);
        this.cerrarModal();
        this.cargar(this.paginaActual());
      },
      error: () => { this.toast.error('Error', 'No se pudo actualizar el envío.'); this.procesando.set(false); },
    });
  }

  confirmarEntregado(id: number) {
    Swal.fire({
      title: '¿Confirmar entrega?',
      text: 'Marca este envío como entregado al estudiante.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, entregado',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.service.update(id, { estado: 'entregado' }).subscribe({
        next: () => { this.toast.success('Entregado', 'Estado actualizado.'); this.cargar(this.paginaActual()); },
        error: () => this.toast.error('Error', 'No se pudo actualizar.'),
      });
    });
  }

  eliminar(id: number) {
    Swal.fire({
      title: '¿Eliminar registro?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.service.delete(id).subscribe({
        next: () => { this.toast.success('Eliminado', 'Registro eliminado.'); this.cargar(this.paginaActual()); },
        error: () => this.toast.error('Error', 'No se pudo eliminar.'),
      });
    });
  }

  // ── Helpers visuales ───────────────────────────────────────────────────
  badgeEstado(estado: EstadoEnvio): { label: string; css: string } {
    switch (estado) {
      case 'pendiente':  return { label: 'Pendiente',  css: 'bg-warning/10 text-warning' };
      case 'enviado':    return { label: 'Enviado',    css: 'bg-primary/10 text-primary' };
      case 'entregado':  return { label: 'Entregado',  css: 'bg-success/10 text-success' };
    }
  }

  whatsappUrl(celular: string | null | undefined, envio: EnvioCertificado): string {
    if (!celular) return '';
    let cel = celular.replace(/\D/g, '');
    if (!cel.startsWith('591')) cel = '591' + cel;
    const msg = `Hola ${envio.estudiante_nombre ?? ''}, te informamos que tu certificado fue enviado a ${envio.ciudad_destino}, ${envio.departamento}`
      + (envio.agencia         ? ` vía ${envio.agencia}`           : '')
      + (envio.nro_seguimiento ? ` (guía: ${envio.nro_seguimiento})` : '')
      + '. Cualquier consulta, con gusto te atendemos.';
    return `https://wa.me/${cel}?text=${encodeURIComponent(msg)}`;
  }

  storageUrl(path: string): string {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return `/storage/${path}`;
  }
}
