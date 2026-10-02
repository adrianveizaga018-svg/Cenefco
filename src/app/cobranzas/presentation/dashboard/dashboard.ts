import { Component, inject, signal, OnInit } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { DecimalPipe, DatePipe } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CobranzaService } from '../../application/services/cobranza.service';
import { CursoService } from '../../../cursos/application/services/curso.service';
import { UsuarioService } from '../../../usuarios/application/services/usuario.service';
import { Title } from '@angular/platform-browser';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [ReactiveFormsModule, NgIcon, PageTitle, DecimalPipe, DatePipe],
  templateUrl: './dashboard.html',
})
export class Dashboard implements OnInit {
  private service      = inject(CobranzaService);
  private cursoSvc     = inject(CursoService);
  private usuarioSvc   = inject(UsuarioService);
  private titleService = inject(Title);

  cuotas         = signal<any[]>([]);
  resumen        = signal<any>(null);
  metrics        = signal<any>(null);
  cargando       = signal(false);
  totalRegistros = signal(0);
  paginaActual   = signal(1);

  // Acordeón
  expandidos   = signal<Set<number>>(new Set());
  cuotasPorIns = signal<Map<number, any[]>>(new Map());

  // Opciones para filtros
  programas  = signal<{ id_programa: number; nombre_programa: string }[]>([]);
  vendedores = signal<any[]>([]);

  // ── Modal comprobante ──────────────────────────────────────────────────────
  comprobanteUrl = signal<string | null>(null);

  // ── Modal pago de cuota ────────────────────────────────────────────────────
  cuotaPagar    = signal<any | null>(null);
  pagandoCuota  = signal(false);
  archivoPago   = signal<File | null>(null);
  formPagoCuota = new FormGroup({
    monto_pagado:   new FormControl<number | null>(null, [Validators.required, Validators.min(0)]),
    nro_boleta:     new FormControl(''),
    fecha_deposito: new FormControl(new Date().toISOString().split('T')[0], Validators.required),
    metodo_pago:    new FormControl('deposito', Validators.required),
    tipo_banco_id:  new FormControl<number | null>(null),
  });

  // ── Filtros ────────────────────────────────────────────────────────────────
  filtros = new FormGroup({
    ci:          new FormControl(''),
    estado:      new FormControl(''),
    programa_id: new FormControl<number | null>(null),
    id_vendedor: new FormControl<number | null>(null),
    metodo_pago: new FormControl(''),
    canal_venta: new FormControl(''),
    fecha_desde: new FormControl(''),
    fecha_hasta: new FormControl(''),
  });

  ngOnInit() {
    this.titleService.setTitle('Dashboard de Cobranzas - CENEFCO');
    this.cargarOpciones();
    this.cargarDashboard();
    this.cargarMetrics();
  }

  private cargarOpciones() {
    this.cursoSvc.getAll({ pageSize: 200 }).subscribe({
      next: r => this.programas.set(r.data.map(c => ({ id_programa: (c as any).id_programa, nombre_programa: c.nombre_programa })))
    });
    this.usuarioSvc.getAll({ pageSize: 200 }).subscribe({
      next: r => this.vendedores.set(r.data)
    });
  }

  cargarMetrics() {
    this.service.getMetrics().subscribe({
      next: (res) => this.metrics.set(res),
      error: () => {}
    });
  }

  private filtrosActuales() {
    const f = this.filtros.value;
    return {
      ci:          f.ci          ?? '',
      programa_id: f.programa_id,
      id_vendedor: f.id_vendedor,
      metodo_pago: f.metodo_pago ?? '',
      canal_venta: f.canal_venta ?? '',
      fecha_desde: f.fecha_desde ?? '',
      fecha_hasta: f.fecha_hasta ?? '',
    };
  }

  cargarDashboard(page = 1) {
    this.cargando.set(true);
    this.paginaActual.set(page);
    const f = this.filtros.value;

    this.service.getDashboard({
      page,
      per_page:    20,
      estado:      f.estado      ?? undefined,
      ci:          f.ci          ?? undefined,
      programa_id: f.programa_id ?? undefined,
      id_vendedor: f.id_vendedor ?? undefined,
      metodo_pago: f.metodo_pago ?? undefined,
      canal_venta: f.canal_venta ?? undefined,
      fecha_desde: f.fecha_desde ?? undefined,
      fecha_hasta: f.fecha_hasta ?? undefined,
    }).subscribe({
      next: (res) => {
        this.cuotas.set(res.data);
        this.totalRegistros.set(res.total);
        this.cargando.set(false);
        this.expandidos.set(new Set());
      },
      error: () => this.cargando.set(false)
    });

    this.service.getResumen(this.filtrosActuales()).subscribe({
      next: r => this.resumen.set(r),
      error: () => {}
    });
  }

  // ── Acordeón ───────────────────────────────────────────────────────────────

  toggleExpandir(idIns: number): void {
    const set = new Set(this.expandidos());
    if (set.has(idIns)) {
      set.delete(idIns);
      this.expandidos.set(set);
    } else {
      set.add(idIns);
      this.expandidos.set(set);
      if (!this.cuotasPorIns().has(idIns)) {
        this.cargarCuotasDeInscripcion(idIns);
      }
    }
  }

  private cargarCuotasDeInscripcion(idIns: number): void {
    this.service.getCuotasInscripcion(idIns).subscribe({
      next: (cuotas) => {
        const map = new Map(this.cuotasPorIns());
        map.set(idIns, cuotas);
        this.cuotasPorIns.set(map);
      },
      error: () => {}
    });
  }

  estaExpandido(idIns: number): boolean { return this.expandidos().has(idIns); }
  cuotasDeIns(idIns: number): any[]     { return this.cuotasPorIns().get(idIns) ?? []; }

  aplicarFiltros()  { this.cargarDashboard(1); }
  limpiarFiltros()  { this.filtros.reset(); this.cargarDashboard(1); }
  cambiarPagina(delta: number) {
    const newPage = this.paginaActual() + delta;
    if (newPage > 0) this.cargarDashboard(newPage);
  }

  // ── Modal comprobante ──────────────────────────────────────────────────────

  verComprobante(url: string)  { this.comprobanteUrl.set(url); }
  cerrarComprobante()          { this.comprobanteUrl.set(null); }
  esPdf(url: string): boolean  { return url.toLowerCase().endsWith('.pdf'); }
  storageUrl(path: string): string {
    if (path.startsWith('http')) return path;
    return `/storage/${path}`;
  }

  // ── Modal pago de cuota ────────────────────────────────────────────────────

  abrirPagarCuota(cuota: any, event: Event): void {
    event.stopPropagation();
    this.cuotaPagar.set(cuota);
    this.archivoPago.set(null);
    this.formPagoCuota.reset({
      monto_pagado:   cuota.monto_a_pagar,
      nro_boleta:     '',
      fecha_deposito: new Date().toISOString().split('T')[0],
      metodo_pago:    'deposito',
      tipo_banco_id:  null,
    });
  }

  cerrarPagarCuota(): void {
    this.cuotaPagar.set(null);
    this.archivoPago.set(null);
  }

  onArchivoPagoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    this.archivoPago.set(file);
  }

  confirmarPagoCuota(): void {
    if (this.formPagoCuota.invalid) {
      this.formPagoCuota.markAllAsTouched();
      return;
    }
    const cuota = this.cuotaPagar();
    if (!cuota) return;

    const fd = new FormData();
    const v  = this.formPagoCuota.value;
    fd.append('monto_pagado',   String(v.monto_pagado ?? cuota.monto_a_pagar));
    fd.append('fecha_deposito', v.fecha_deposito ?? '');
    fd.append('metodo_pago',    v.metodo_pago ?? 'efectivo');
    if (v.nro_boleta)    fd.append('nro_boleta',    v.nro_boleta);
    if (v.tipo_banco_id) fd.append('tipo_banco_id', String(v.tipo_banco_id));
    if (this.archivoPago()) fd.append('comprobante', this.archivoPago()!);

    this.pagandoCuota.set(true);
    this.service.pagarCuota(cuota.id, fd).subscribe({
      next: () => {
        this.pagandoCuota.set(false);
        this.cerrarPagarCuota();
        Swal.fire({ icon: 'success', title: 'Pago registrado', text: 'La cuota fue marcada como pagada.', timer: 2000, showConfirmButton: false });
        // Refrescar cuotas y totales
        const idIns: number = cuota.id_ins;
        const map = new Map(this.cuotasPorIns());
        map.delete(idIns);
        this.cuotasPorIns.set(map);
        this.cargarCuotasDeInscripcion(idIns);
        this.cargarDashboard(this.paginaActual());
      },
      error: (err) => {
        this.pagandoCuota.set(false);
        const data = err.error ?? {};
        if (err.status === 422 && data.boleta_duplicada) {
          Swal.fire({ icon: 'warning', title: 'Boleta duplicada', html: `${data.message ?? ''}<br><small>${data.registrado_por ?? ''} · ${data.registrado_el ?? ''}</small>` });
        } else {
          Swal.fire('Error', data.message ?? 'No se pudo registrar el pago', 'error');
        }
      },
    });
  }

  // ── Helpers ────────────────────────────────────────────────────────────────

  isVencida(fecha: string): boolean {
    if (!fecha) return false;
    const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
    const f   = new Date(fecha); f.setHours(0, 0, 0, 0);
    return f < hoy;
  }

  getWhatsAppUrl(alerta: any, tipo: 'vence_pronto' | 'vencido'): string {
    if (!alerta.celular) return '';
    let cel = alerta.celular.replace(/\D/g, '');
    if (!cel.startsWith('591')) cel = '591' + cel;
    const msg = tipo === 'vence_pronto'
      ? `Hola ${alerta.estudiante_nombre}, te saludamos de CENEFCO. Tu cuota de Bs. ${alerta.monto} por "${alerta.descripcion}" vence el ${alerta.fecha_vencimiento}.`
      : `Hola ${alerta.estudiante_nombre}, te saludamos de CENEFCO. Tu cuota de Bs. ${alerta.monto} por "${alerta.descripcion}" ya está vencida. Por favor regulariza tu pago.`;
    return `https://wa.me/${cel}?text=${encodeURIComponent(msg)}`;
  }

  getWhatsAppUrlCuota(c: any): string {
    if (!c.celular) return '';
    let cel = c.celular.replace(/\D/g, '');
    if (!cel.startsWith('591')) cel = '591' + cel;
    const msg = `Hola ${c.nombre} ${c.appaterno ?? ''}, te recordamos de CENEFCO que tienes pendiente la cuota "${c.descripcion}" de Bs. ${c.monto_a_pagar} con vencimiento ${c.fecha_vencimiento}.`;
    return `https://wa.me/${cel}?text=${encodeURIComponent(msg)}`;
  }

  badgeEstado(c: any): { label: string; css: string } {
    if (c.estado === 'pagado')               return { label: 'Pagado',    css: 'bg-success/10 text-success' };
    if (this.isVencida(c.fecha_vencimiento)) return { label: 'Vencida',   css: 'bg-danger/10 text-danger' };
    return                                          { label: 'Pendiente', css: 'bg-warning/10 text-warning' };
  }

  canalLabel(c: string | null): string {
    const map: Record<string, string> = {
      admin: 'Presencial', portal: 'Portal', whatsapp: 'WhatsApp',
      referido: 'Referido', presencial: 'Presencial',
    };
    return c ? (map[c] ?? c) : '—';
  }

  metodoPagoLabel(m: string | null): string {
    const map: Record<string, string> = {
      efectivo: 'Efectivo', deposito_bancario: 'Depósito',
      deposito: 'Depósito', qr: 'QR', transferencia: 'Transferencia',
    };
    return m ? (map[m] ?? m) : '—';
  }
}
