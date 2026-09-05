import { Component, inject, signal, computed, OnInit, ChangeDetectorRef } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import * as XLSX from 'xlsx';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { VentaService } from '../../application/services/venta.service';
import { Venta, MetodoPago } from '../../domain/models/venta.model';

interface FilaMes {
  mes: string; key: string; inscritos: number;
  pagado: number; pendiente: number;
  pagados: number; parciales: number; pendi: number;
}
interface FilaCanal { canal: string; inscritos: number; pagado: number; }
interface FilaMetodo { metodo: string; cantidad: number; monto: number; }
interface FilaVendedor {
  id_vendedor: number | null; vendedor_nombre: string;
  inscritos: number; total_cobrado: number; total_pendiente: number; total_plan: number;
  pagados: number; parciales: number; pendientes: number;
}
interface FilaProyeccion { mes_key: string; gestion: string; cuotas_pendientes: number; monto_proyectado: number; }

const MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const CANAL_LABEL: Record<string,string> = { admin:'Admin', portal:'Portal', whatsapp:'WhatsApp', referido:'Referido' };
const METODO_LABEL: Record<string,string> = { efectivo:'Efectivo', deposito_bancario:'Depósito Bancario', pago_online:'Pago Online', qr:'QR' };
const CANAL_COLORS = ['#07435B','#128AA2','#FC8900','#34d399'];

@Component({
  selector: 'app-reporte-ventas',
  imports: [DecimalPipe, FormsModule, NgIcon, PageTitle],
  templateUrl: './reporte-ventas.html',
})
export class ReporteVentas implements OnInit {
  private service = inject(VentaService);
  private cdr     = inject(ChangeDetectorRef);

  gestionInput = signal<number>(new Date().getFullYear());
  periodoInput = signal('');
  cargando     = signal(false);
  datos        = signal<Venta[]>([]);

  vendedores    = signal<FilaVendedor[]>([]);
  proyeccion    = signal<FilaProyeccion[]>([]);
  cargandoExtra = signal(false);

  readonly gestionActual = new Date().getFullYear();
  readonly gestiones = Array.from({ length: 5 }, (_, i) => this.gestionActual - i);

  readonly totales = computed(() => {
    const d = this.datos();
    return {
      inscritos: d.length,
      pagado:    d.reduce((s,v) => s + v.total_pagado, 0),
      pendiente: d.reduce((s,v) => s + v.saldo_pendiente, 0),
      aPagar:    d.reduce((s,v) => s + v.total_a_pagar, 0),
      pagados:   d.filter(v => v.estado_pago === 'pagado').length,
      parciales: d.filter(v => v.estado_pago === 'parcial').length,
      pendi:     d.filter(v => v.estado_pago === 'pendiente').length,
    };
  });

  readonly porMes = computed((): FilaMes[] => {
    const mapa = new Map<string, FilaMes>();
    for (const v of this.datos()) {
      const fecha = v.fecha_reg ?? v.fecha_ins ?? '';
      if (!fecha) continue;
      const d = new Date(fecha);
      const key = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`;
      const mes = `${MESES[d.getMonth()]} ${d.getFullYear()}`;
      if (!mapa.has(key)) mapa.set(key, { mes, key, inscritos:0, pagado:0, pendiente:0, pagados:0, parciales:0, pendi:0 });
      const f = mapa.get(key)!;
      f.inscritos++; f.pagado += v.total_pagado; f.pendiente += v.saldo_pendiente;
      if (v.estado_pago === 'pagado')    f.pagados++;
      if (v.estado_pago === 'parcial')   f.parciales++;
      if (v.estado_pago === 'pendiente') f.pendi++;
    }
    return [...mapa.entries()].sort((a,b) => a[0].localeCompare(b[0])).map(([,v]) => v);
  });

  readonly porMetodo = computed((): FilaMetodo[] => {
    const mapa = new Map<string, FilaMetodo>();
    for (const v of this.datos()) {
      for (const p of (v as any).pagos ?? []) {
        const m = p.metodo_pago ?? 'efectivo';
        if (!mapa.has(m)) mapa.set(m, { metodo: m, cantidad: 0, monto: 0 });
        const f = mapa.get(m)!; f.cantidad++; f.monto += Number(p.monto_pagado ?? 0);
      }
    }
    return [...mapa.values()].sort((a,b) => b.monto - a.monto);
  });

  readonly porCanal = computed((): FilaCanal[] => {
    const mapa = new Map<string, FilaCanal>();
    for (const v of this.datos()) {
      const canal = v.canal_venta ?? 'admin';
      if (!mapa.has(canal)) mapa.set(canal, { canal, inscritos:0, pagado:0 });
      const f = mapa.get(canal)!; f.inscritos++; f.pagado += v.total_pagado;
    }
    return [...mapa.values()].sort((a,b) => b.inscritos - a.inscritos);
  });

  readonly porcentajeCobro = computed(() => {
    const t = this.totales();
    return t.aPagar === 0 ? 0 : Math.round((t.pagado / t.aPagar) * 100);
  });

  readonly chartData = computed(() => {
    const filas = this.porMes();
    if (filas.length === 0) return null;
    const maxPagado = Math.max(...filas.map(f => f.pagado + f.pendiente), 1);
    const W = 560, H = 180, PAD = 8;
    const BAR_W = Math.min(28, Math.floor((W - PAD*2) / filas.length) - 4);
    const barras = filas.map((f, i) => {
      const slotW = (W - PAD*2) / filas.length;
      const x = PAD + i * slotW + (slotW - BAR_W) / 2;
      const hPagado = Math.round((f.pagado / maxPagado) * (H - 30));
      const hPendi  = Math.round((f.pendiente / maxPagado) * (H - 30));
      return { x, hPagado, hPendi, mes: f.mes.split(' ')[0] };
    });
    return { barras, W, H, BAR_W };
  });

  readonly chartVendedorData = computed(() => {
    const filas = this.vendedores().slice(0, 8);
    if (filas.length === 0) return null;
    const maxCobrado = Math.max(...filas.map(f => f.total_cobrado + f.total_pendiente), 1);
    const W = 560, H = 160, PAD = 8;
    const BAR_W = Math.min(40, Math.floor((W - PAD*2) / filas.length) - 6);
    const barras = filas.map((f, i) => {
      const slotW = (W - PAD*2) / filas.length;
      const x = PAD + i * slotW + (slotW - BAR_W) / 2;
      const hCob  = Math.round((f.total_cobrado  / maxCobrado) * (H - 30));
      const hPend = Math.round((f.total_pendiente / maxCobrado) * (H - 30));
      const nombre = (f.vendedor_nombre || 'N/A').split(' ')[0];
      return { x, hCob, hPend, nombre };
    });
    return { barras, W, H, BAR_W };
  });

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.cargando.set(true);
    this.cargandoExtra.set(true);

    const params = { gestion: this.gestionInput(), periodo: this.periodoInput() || undefined };

    this.service.getReporte({ gestion: this.gestionInput(), periodo: this.periodoInput() || undefined }).subscribe({
      next: res => { this.datos.set(res.data); this.cargando.set(false); this.cdr.detectChanges(); },
      error: () => { this.cargando.set(false); this.cdr.detectChanges(); },
    });

    this.service.getReportePorVendedor(params).subscribe({
      next: res => { this.vendedores.set(res.data); this.cdr.detectChanges(); },
      error: () => {},
    });

    this.service.getProyeccionCobros(6).subscribe({
      next: res => { this.proyeccion.set(res.data); this.cargandoExtra.set(false); this.cdr.detectChanges(); },
      error: () => { this.cargandoExtra.set(false); this.cdr.detectChanges(); },
    });
  }

  exportarExcel(): void {
    const filasMes = this.porMes().map(f => ({
      'Mes': f.mes, 'Inscripciones': f.inscritos,
      'Pagado (Bs.)': f.pagado, 'Pendiente (Bs.)': f.pendiente,
      'Pagados': f.pagados, 'Parciales': f.parciales, 'Pendientes': f.pendi,
    }));
    const filasDetalle = this.datos().map(v => ({
      'CI': v.estudiante_ci, 'Estudiante': v.estudiante_nombre, 'Programa': v.nombre_programa,
      'Período': v.periodo, 'Gestión': v.gestion,
      'Canal': CANAL_LABEL[v.canal_venta ?? ''] ?? v.canal_venta,
      'Total Plan (Bs.)': v.total_a_pagar, 'Pagado (Bs.)': v.total_pagado,
      'Saldo (Bs.)': v.saldo_pendiente, 'Estado': v.estado_pago,
      'Fecha Inscripción': v.fecha_ins,
    }));
    const filasVend = this.vendedores().map(v => ({
      'Vendedor': v.vendedor_nombre, 'Inscripciones': v.inscritos,
      'Cobrado (Bs.)': v.total_cobrado, 'Pendiente (Bs.)': v.total_pendiente,
      'Plan Total (Bs.)': v.total_plan, 'Pagados': v.pagados,
      'Parciales': v.parciales, 'Pendientes': v.pendientes,
    }));
    const filasProy = this.proyeccion().map(p => ({
      'Mes': p.mes_key, 'Cuotas Pendientes': p.cuotas_pendientes,
      'Monto Proyectado (Bs.)': p.monto_proyectado,
    }));
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(filasMes), 'Por Mes');
    XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(filasVend), 'Por Vendedor');
    XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(filasProy), 'Proyección Cobros');
    XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(filasDetalle), 'Detalle');
    XLSX.writeFile(wb, `reporte-ventas-${this.gestionInput()}.xlsx`);
  }

  readonly totalProyectadoCuotas = computed(() =>
    this.proyeccion().reduce((s, p) => s + Number(p.cuotas_pendientes), 0)
  );

  readonly totalProyectadoMonto = computed(() =>
    this.proyeccion().reduce((s, p) => s + Number(p.monto_proyectado), 0)
  );

  mesLabel(mesKey: string): string {
    const [anio, mes] = mesKey.split('-');
    return `${MESES[Number(mes) - 1]} ${anio}`;
  }

  canalLabel(c: string): string { return CANAL_LABEL[c] ?? c; }
  canalColor(i: number): string { return CANAL_COLORS[i % CANAL_COLORS.length]; }
  metodoLabel(m: string): string { return METODO_LABEL[m] ?? m; }
}
