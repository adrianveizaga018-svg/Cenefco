import { Component, computed, inject, signal } from '@angular/core';
import { DecimalPipe, NgClass, SlicePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, firstValueFrom, map, of, startWith, switchMap } from 'rxjs';
import * as XLSX from 'xlsx';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CursoService } from '../../application/services/curso.service';
import { ZoomService } from '../../application/services/zoom.service';
import { AlertaCobrosCurso, Curso, CursoEstadisticas, CursoListResponse, EnvioDocumento, PeriodoEstadisticas, PlanCobrosCurso } from '../../domain/models/curso.model';
import { ZoomMeeting, ZoomRecording } from '../../domain/models/zoom.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { CertificadoService } from '../../../certificados/application/services/certificado.service';
import { CertPlantilla, GenerarLoteResult } from '../../../certificados/domain/models/certificado.model';
import { CompromisoCobroService } from '../../../compromisos-cobro/application/services/compromiso-cobro.service';
import { ResumenCompromisosCobro } from '../../../compromisos-cobro/domain/models/compromiso-cobro.model';
import { ReporteCobrosService } from '../../../reportes-cobros/application/services/reporte-cobros.service';
import { ReporteCobrosResponse } from '../../../reportes-cobros/domain/models/reporte-cobros.model';
import { FechaPagoService } from '../../../fechas-pago/application/services/fecha-pago.service';
import { FechaPago } from '../../../fechas-pago/domain/models/fecha-pago.model';
import { AsistenteCuotas } from '../../../fechas-pago/presentation/asistente-cuotas/asistente-cuotas';
import { extractErrorMessage } from '../../../utils/http-error';
import { portalUrl } from '../../../constants';
import Swal from 'sweetalert2';
import QRCode from 'qrcode';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: CursoListResponse }
  | { type: 'error' } | { type: 'forbidden' };

type StatsState =
  | { type: 'loading' }
  | { type: 'success'; data: CursoEstadisticas }
  | { type: 'error' };

const STATS_LOADING: StatsState = { type: 'loading' };
const STATS_ERROR:   StatsState = { type: 'error' };

type ZoomVista = 'reuniones' | 'grabaciones' | 'nueva';

interface FilaCuota {
  curso_nombre:        string;
  estudiante_nombre:   string;
  estudiante_ci:       string | null;
  estudiante_email:    string | null;
  estudiante_celular:  string | null;
  cuota_nro:           string | null;
  cuota_monto:         number | null;
  monto_pagado:        number;
  estado:              string;
  fecha_limite:        string | null;
  dias_atraso:         number;
}

const CUOTA_ESTADO_LABELS: Record<string, string> = {
  pagada: 'Pagada', parcial: 'Parcial', vencida: 'Vencida', pendiente: 'Pendiente',
  al_dia: 'Al día', en_mora: 'En mora', completo: 'Completo', sin_pagos: 'Sin pagos', sin_plan: 'Sin plan de pagos',
};

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-cursos',
  imports: [NgIcon, Pagination, PageTitle, RouterLink, DecimalPipe, SlicePipe, NgClass, AsistenteCuotas, FormsModule],
  templateUrl: './cursos.html',
  styles: ``
})
export class Cursos {
  private cursoService  = inject(CursoService);
  private zoomService   = inject(ZoomService);
  private certService   = inject(CertificadoService);
  private toast         = inject(ToastService);
  private auth          = inject(AuthService);
  private compromisoCobroService = inject(CompromisoCobroService);
  private reporteCobrosService   = inject(ReporteCobrosService);
  private fechaPagoService       = inject(FechaPagoService);

  get puedeCrear(): boolean { return this.auth.hasPermission('contenido.crear'); }
  get puedeEditar(): boolean { return this.auth.hasPermission('contenido.editar'); }
  get puedeEliminar(): boolean { return this.auth.hasPermission('contenido.eliminar'); }
  get puedeVerCompromisos(): boolean { return this.auth.hasPermission('compromisos-cobro.ver'); }

  compromisoResumen = signal<ResumenCompromisosCobro | null>(null);
  private alertasCobros = signal<Map<number, AlertaCobrosCurso>>(new Map());

  constructor() {
    if (this.puedeVerCompromisos) {
      this.compromisoCobroService.getResumen().subscribe({
        next: r => this.compromisoResumen.set(r),
        error: () => {},
      });
    }

    this.cursoService.getAlertasCobros().subscribe({
      next: (alertas) => this.alertasCobros.set(new Map(alertas.map(a => [a.id_imp, a]))),
      error: () => {},
    });
  }

  alertaCobrosPara(idImp: number | null): AlertaCobrosCurso | undefined {
    return idImp ? this.alertasCobros().get(idImp) : undefined;
  }

  alertaCobrosTitulo(idImp: number | null): string {
    const alerta = this.alertaCobrosPara(idImp);
    if (!alerta) return 'Programar Cobros (cuotas y fechas de vencimiento)';
    return alerta.vencidas > 0
      ? `${alerta.vencidas} cuota(s) vencida(s) sin pago`
      : `${alerta.proximas} cuota(s) por vencer pronto`;
  }

  searchQuery    = signal('');
  pageIndex      = signal(1);
  pageSize       = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query:     this.searchQuery(),
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    refresh:   this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.cursoService.getAll(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith(LOADING),
          catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
        )
      ),
      startWith(LOADING),
    ),
    { requireSync: true }
  );

  get cursos()    { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }

  readonly periodos: { value: PeriodoEstadisticas; label: string }[] = [
    { value: 'dia',   label: 'Día' },
    { value: 'mes',   label: 'Mes' },
    { value: 'anio',  label: 'Año' },
    { value: 'rango', label: 'Rango' },
  ];

  periodoStats  = signal<PeriodoEstadisticas>('mes');
  fechaStats    = signal(this.hoyIso());
  fechaStatsFin = signal(this.hoyIso());

  private statsParams = computed(() => ({
    periodo:  this.periodoStats(),
    fecha:    this.fechaStats(),
    fechaFin: this.periodoStats() === 'rango' ? this.fechaStatsFin() : undefined,
  }));

  private statsState = toSignal(
    toObservable(this.statsParams).pipe(
      switchMap(p =>
        this.cursoService.getEstadisticas(p.periodo, p.fecha, p.fechaFin).pipe(
          map(data => ({ type: 'success', data } as StatsState)),
          startWith(STATS_LOADING),
          catchError(() => of(STATS_ERROR)),
        )
      ),
      startWith(STATS_LOADING),
    ),
    { requireSync: true }
  );

  get statsLoading() { return this.statsState().type === 'loading'; }
  get statsError()   { return this.statsState().type === 'error'; }
  get stats()        { const s = this.statsState(); return s.type === 'success' ? s.data : null; }

  private hoyIso(): string {
    return new Date().toISOString().split('T')[0];
  }

  cambiarPeriodoStats(periodo: PeriodoEstadisticas): void {
    this.periodoStats.set(periodo);
    if (periodo === 'rango' && this.fechaStatsFin() < this.fechaStats()) {
      this.fechaStatsFin.set(this.fechaStats());
    }
  }

  onFechaStatsInput(event: Event, periodo: PeriodoEstadisticas): void {
    const valor = (event.target as HTMLInputElement).value;
    if (!valor) return;

    if (periodo === 'mes') {
      this.fechaStats.set(`${valor}-01`);
    } else if (periodo === 'anio') {
      this.fechaStats.set(`${valor}-01-01`);
    } else {
      this.fechaStats.set(valor);

      if (periodo === 'rango' && this.fechaStatsFin() < valor) {
        this.fechaStatsFin.set(valor);
      }
    }
  }

  onFechaStatsFinInput(event: Event): void {
    const valor = (event.target as HTMLInputElement).value;
    if (!valor) return;
    this.fechaStatsFin.set(valor);



    if (valor < this.fechaStats()) {
      this.fechaStats.set(valor);
    }
  }

  get fechaStatsMes(): string {
    return this.fechaStats().slice(0, 7);
  }

  get fechaStatsAnio(): string {
    return this.fechaStats().slice(0, 4);
  }

  private labelPeriodoStats(periodo: PeriodoEstadisticas, fechaInicio: string, fechaFin?: string): string {
    const inicio = new Date(fechaInicio + 'T12:00:00');
    if (periodo === 'rango') {
      const fin = new Date((fechaFin ?? fechaInicio) + 'T12:00:00');
      const fmt = (d: Date) => d.toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' });
      return `${fmt(inicio)} al ${fmt(fin)}`;
    }
    if (periodo === 'dia') {
      return inicio.toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });
    }
    if (periodo === 'anio') {
      return `Año ${inicio.getFullYear()}`;
    }
    return inicio.toLocaleDateString('es-BO', { month: 'long', year: 'numeric' });
  }

  exportandoExcelStats = signal(false);
  exportandoPdfStats   = signal(false);

  exportarEstadisticasExcel(): void {
    this.exportandoExcelStats.set(true);

    this.cursoService.getEstadisticasDetalle(
      this.periodoStats(), this.fechaStats(),
      this.periodoStats() === 'rango' ? this.fechaStatsFin() : undefined
    ).subscribe({
      next: (d) => {
        this.exportandoExcelStats.set(false);

        const hojaResumen = XLSX.utils.json_to_sheet([{
          'Período':              this.labelPeriodoStats(d.periodo, d.fecha_inicio, d.fecha_fin),
          'Desde':                d.fecha_inicio,
          'Hasta':                d.fecha_fin,
          'Total Inscritos':      d.total_inscritos,
          'Total Ingresos (Bs.)': d.total_ingresos,
        }]);

        const hojaInscritos = XLSX.utils.json_to_sheet(d.inscritos.map(i => ({
          'Fecha Inscripción': i.fecha_ins_efectiva,
          'Estudiante':        i.estudiante_nombre,
          'CI':                i.estudiante_ci ?? '',
          'Curso':             i.curso_nombre ?? '',
          'Canal':             i.canal_venta,
          'Estado':            i.estado === 1 ? 'Activo' : 'Baja',
        })));

        const hojaPagos = XLSX.utils.json_to_sheet(d.pagos.map(p => ({
          'Fecha Pago':  p.fecha_deposito,
          'Estudiante':  p.estudiante_nombre,
          'CI':          p.estudiante_ci ?? '',
          'Curso':       p.curso_nombre ?? '',
          'Método':      p.metodo_pago,
          'Monto (Bs.)': p.monto_pagado,
        })));

        const libro = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(libro, hojaResumen, 'Resumen');
        XLSX.utils.book_append_sheet(libro, hojaInscritos, 'Inscritos');
        XLSX.utils.book_append_sheet(libro, hojaPagos, 'Pagos');
        XLSX.writeFile(libro, `estadisticas_cursos_${d.fecha_inicio}_al_${d.fecha_fin}.xlsx`);
      },
      error: () => {
        this.exportandoExcelStats.set(false);
        this.toast.error('Error', 'No se pudo generar el reporte Excel');
      }
    });
  }

  exportarEstadisticasPDF(): void {




    const ventana = window.open('', '_blank', 'width=1200,height=820');
    if (!ventana) {
      this.toast.error('Bloqueado', 'Permite ventanas emergentes para generar el reporte PDF');
      return;
    }
    ventana.document.write('<p style="font-family:sans-serif;padding:20px;">Generando reporte…</p>');

    this.exportandoPdfStats.set(true);

    const logoPromise = fetch('/assets/images/logo-dark.png')
      .then(r => r.blob())
      .then(blob => new Promise<string>(resolve => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result as string);
        reader.readAsDataURL(blob);
      }))
      .catch(() => '');

    Promise.all([
      firstValueFrom(this.cursoService.getEstadisticasDetalle(
        this.periodoStats(), this.fechaStats(),
        this.periodoStats() === 'rango' ? this.fechaStatsFin() : undefined
      )),
      logoPromise,
    ]).then(([d, logoBase64]) => {
      this.exportandoPdfStats.set(false);
      if (!d) return;

      const ahora = new Date();
      const fecha = ahora.toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });
      const hora  = ahora.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });

      const fmtFecha = (f: string) => new Date(f + 'T12:00:00').toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' });
      const fmtMonto = (n: number) => new Intl.NumberFormat('es-BO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(n);

      const filasInscritos = d.inscritos.map((i, idx) => `
        <tr class="${idx % 2 === 0 ? '' : 'alt'}">
          <td class="center">${fmtFecha(i.fecha_ins_efectiva)}</td>
          <td class="bold">${i.estudiante_nombre}</td>
          <td>${i.estudiante_ci ?? '—'}</td>
          <td>${i.curso_nombre ?? '—'}</td>
          <td class="center">${i.canal_venta}</td>
          <td class="center">
            <span class="${i.estado === 1 ? 'badge-pub' : 'badge-bor'}">${i.estado === 1 ? 'Activo' : 'Baja'}</span>
          </td>
        </tr>`).join('');

      const filasPagos = d.pagos.map((p, idx) => `
        <tr class="${idx % 2 === 0 ? '' : 'alt'}">
          <td class="center">${fmtFecha(p.fecha_deposito)}</td>
          <td class="bold">${p.estudiante_nombre}</td>
          <td>${p.curso_nombre ?? '—'}</td>
          <td>${p.metodo_pago}</td>
          <td class="center bold">Bs. ${fmtMonto(p.monto_pagado)}</td>
        </tr>`).join('');

      const logoHtml = logoBase64
        ? `<img src="${logoBase64}" alt="CENEFCO" style="height:52px; object-fit:contain;" />`
        : `<div class="logo-fallback">CENEFCO</div>`;

      const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Inscripciones e Ingresos — CENEFCO</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Arial', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
    .page-header { display: flex; align-items: stretch; border-bottom: 4px solid #1e3a5f; }
    .header-logo { padding: 16px 24px; display: flex; align-items: center; background: #fff; border-right: 1px solid #e2e8f0; }
    .logo-fallback { font-size: 22px; font-weight: 900; color: #1e3a5f; letter-spacing: -1px; }
    .header-info { flex: 1; padding: 12px 20px; background: #1e3a5f; color: #fff; display: flex; flex-direction: column; justify-content: center; }
    .header-info .report-title { font-size: 15px; font-weight: 700; letter-spacing: 0.3px; text-transform: uppercase; }
    .header-info .report-subtitle { font-size: 9px; color: #93c5fd; margin-top: 2px; }
    .header-meta { padding: 12px 20px; background: #f0f4f8; border-left: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: center; gap: 2px; text-align: right; min-width: 160px; }
    .header-meta .meta-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
    .header-meta .meta-val { font-size: 9px; color: #1e293b; font-weight: 600; }
    .band { height: 5px; background: linear-gradient(90deg, #1e3a5f 0%, #2563eb 50%, #38bdf8 100%); }
    .summary { display: flex; gap: 0; border-bottom: 1px solid #e2e8f0; }
    .summary-card { flex: 1; padding: 12px 18px; border-right: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; }
    .summary-card:last-child { border-right: none; }
    .summary-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .icon-periodo { background: #dbeafe; color: #1d4ed8; }
    .icon-inscritos { background: #ede9fe; color: #6d28d9; }
    .icon-ingresos { background: #dcfce7; color: #15803d; }
    .summary-text .s-num { font-size: 18px; font-weight: 800; color: #0f172a; line-height: 1; }
    .summary-text .s-label { font-size: 8.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 1px; }
    .section-header { padding: 14px 20px 6px; display: flex; align-items: center; gap: 8px; }
    .section-line { flex: 1; height: 1px; background: #e2e8f0; }
    .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #475569; white-space: nowrap; }
    .table-wrap { padding: 0 20px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a5f; }
    thead th { padding: 8px 8px; text-align: left; font-size: 8.5px; font-weight: 700; color: #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    thead th.center { text-align: center; }
    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr.alt { background: #f8fafc; }
    tbody td { padding: 6.5px 8px; font-size: 9px; vertical-align: middle; color: #334155; }
    td.center { text-align: center; }
    td.bold { font-weight: 600; color: #0f172a; }
    .empty-msg { padding: 16px 20px; font-size: 9px; color: #94a3b8; text-align: center; }
    .badge-pub { display: inline-block; padding: 2px 8px; border-radius: 12px; background: #dcfce7; color: #15803d; font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
    .badge-bor { display: inline-block; padding: 2px 8px; border-radius: 12px; background: #f1f5f9; color: #94a3b8; font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
    .page-footer { margin-top: 16px; padding: 10px 20px; border-top: 2px solid #1e3a5f; display: flex; align-items: center; justify-content: space-between; }
    .page-footer .footer-left { font-size: 8px; color: #64748b; }
    .page-footer .footer-right { font-size: 8px; color: #94a3b8; text-align: right; }
    .page-footer .footer-confidencial { font-size: 7.5px; color: #94a3b8; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-print-bar { position: fixed; bottom: 20px; right: 20px; display: flex; gap: 8px; z-index: 999; }
    .btn-print { padding: 10px 22px; background: #1e3a5f; color: #fff; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .btn-print:hover { background: #2563eb; }
    .btn-close { padding: 10px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; cursor: pointer; }
    @page { size: A4 landscape; margin: 12mm 10mm; }
    @media print {
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .btn-print-bar { display: none !important; }
      thead { display: table-header-group; }
      tbody tr { page-break-inside: avoid; }
    }
  </style>
</head>
<body>

  <div class="page-header">
    <div class="header-logo">${logoHtml}</div>
    <div class="header-info">
      <div class="report-title">Reporte de Inscripciones e Ingresos</div>
      <div class="report-subtitle">Centro Nacional de Educación y Formación Continua — Unidad Académica</div>
    </div>
    <div class="header-meta">
      <span class="meta-label">Fecha de emisión</span>
      <span class="meta-val">${fecha}</span>
      <span class="meta-label" style="margin-top:6px;">Hora</span>
      <span class="meta-val">${hora}</span>
    </div>
  </div>

  <div class="band"></div>

  <div class="summary">
    <div class="summary-card">
      <div class="summary-icon icon-periodo">📅</div>
      <div class="summary-text">
        <div class="s-num" style="font-size:13px;">${this.labelPeriodoStats(d.periodo, d.fecha_inicio, d.fecha_fin)}</div>
        <div class="s-label">${fmtFecha(d.fecha_inicio)} al ${fmtFecha(d.fecha_fin)}</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-inscritos">👥</div>
      <div class="summary-text">
        <div class="s-num">${d.total_inscritos}</div>
        <div class="s-label">Total Inscritos</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-ingresos">💰</div>
      <div class="summary-text">
        <div class="s-num">Bs. ${fmtMonto(d.total_ingresos)}</div>
        <div class="s-label">Total Ingresos</div>
      </div>
    </div>
  </div>

  <div class="section-header">
    <span class="section-title">Inscritos del período</span>
    <div class="section-line"></div>
    <span style="font-size:8px; color:#94a3b8; white-space:nowrap; margin-left:8px;">${d.total_inscritos} registro(s)</span>
  </div>

  <div class="table-wrap">
    ${d.inscritos.length === 0 ? '<div class="empty-msg">Sin inscripciones en este período.</div>' : `
    <table>
      <thead>
        <tr>
          <th class="center" style="width:70px;">Fecha</th>
          <th>Estudiante</th>
          <th>CI</th>
          <th>Curso</th>
          <th class="center">Canal</th>
          <th class="center">Estado</th>
        </tr>
      </thead>
      <tbody>${filasInscritos}</tbody>
    </table>`}
  </div>

  <div class="section-header">
    <span class="section-title">Pagos del período</span>
    <div class="section-line"></div>
    <span style="font-size:8px; color:#94a3b8; white-space:nowrap; margin-left:8px;">${d.pagos.length} registro(s)</span>
  </div>

  <div class="table-wrap">
    ${d.pagos.length === 0 ? '<div class="empty-msg">Sin pagos registrados en este período.</div>' : `
    <table>
      <thead>
        <tr>
          <th class="center" style="width:70px;">Fecha</th>
          <th>Estudiante</th>
          <th>Curso</th>
          <th>Método</th>
          <th class="center">Monto</th>
        </tr>
      </thead>
      <tbody>${filasPagos}</tbody>
    </table>`}
  </div>

  <div class="page-footer">
    <div class="footer-left">
      <strong>CENEFCO</strong> — Centro Nacional de Educación y Formación Continua<br>
      Documento generado automáticamente por el Sistema de Gestión Académica
    </div>
    <div class="footer-confidencial">Documento de uso interno — Confidencial</div>
    <div class="footer-right">
      Emitido: ${fecha} · ${hora}<br>
      Inscritos: ${d.total_inscritos} · Ingresos: Bs. ${fmtMonto(d.total_ingresos)}
    </div>
  </div>

  <div class="btn-print-bar">
    <button class="btn-close" onclick="window.close()">Cerrar</button>
    <button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button>
  </div>

  <script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
  </script>
</body>
</html>`;

      ventana.document.open();
      ventana.document.write(html);
      ventana.document.close();
    }).catch(() => {
      this.exportandoPdfStats.set(false);
      ventana.close();
      this.toast.error('Error', 'No se pudo generar el reporte PDF');
    });
  }

  reporteDocAbierto         = signal(false);
  reporteDocFechaInicio     = signal('');
  reporteDocFechaFin        = signal('');
  reporteDocCargando        = signal(false);
  reporteDocError           = signal(false);
  reporteDocDatos           = signal<EnvioDocumento[]>([]);
  reporteDocExportandoExcel = signal(false);
  reporteDocExportandoPdf   = signal(false);

  abrirReporteDocumentos(): void {
    this.reporteDocAbierto.set(true);
    this.reporteDocFechaInicio.set('');
    this.reporteDocFechaFin.set('');
    this.buscarReporteDocumentos();
  }

  cerrarReporteDocumentos(): void {
    this.reporteDocAbierto.set(false);
  }

  onReporteDocFechaInicioInput(event: Event): void {
    const valor = (event.target as HTMLInputElement).value;
    this.reporteDocFechaInicio.set(valor);
    if (valor && this.reporteDocFechaFin() && this.reporteDocFechaFin() < valor) {
      this.reporteDocFechaFin.set(valor);
    }
  }

  onReporteDocFechaFinInput(event: Event): void {
    const valor = (event.target as HTMLInputElement).value;
    this.reporteDocFechaFin.set(valor);
    if (valor && this.reporteDocFechaInicio() && this.reporteDocFechaInicio() > valor) {
      this.reporteDocFechaInicio.set(valor);
    }
  }

  buscarReporteDocumentos(): void {
    this.reporteDocCargando.set(true);
    this.reporteDocError.set(false);
    this.cursoService.getReporteEnviosDocumentos(
      this.reporteDocFechaInicio() || undefined,
      this.reporteDocFechaFin() || undefined,
    ).subscribe({
      next: (r) => {
        this.reporteDocDatos.set(r.envios);
        this.reporteDocCargando.set(false);
      },
      error: () => {
        this.reporteDocDatos.set([]);
        this.reporteDocCargando.set(false);
        this.reporteDocError.set(true);
      }
    });
  }

  exportarReporteDocumentosExcel(): void {
    const filas = this.reporteDocDatos();
    if (!filas.length) {
      this.toast.warning('Sin datos', 'No hay envíos de documentos para exportar');
      return;
    }

    this.reporteDocExportandoExcel.set(true);

    const datos = filas.map(e => ({
      'Curso':        e.curso_nombre ?? '',
      'Participante': e.participante_nombre,
      'CI':           e.participante_ci ?? '',
      'Ciudad':       e.participante_ciudad ?? '',
      'Documento':    e.tipo_documento ?? '',
      'Fecha':        e.fecha_envio,
    }));

    const hoja  = XLSX.utils.json_to_sheet(datos);
    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, 'Envíos de Documentos');
    XLSX.writeFile(libro, `envios_documentos_${this.hoyIso()}.xlsx`);

    this.reporteDocExportandoExcel.set(false);
  }

  exportarReporteDocumentosPDF(): void {
    const filas = this.reporteDocDatos();
    if (!filas.length) {
      this.toast.warning('Sin datos', 'No hay envíos de documentos para exportar');
      return;
    }

    const ventana = window.open('', '_blank', 'width=1200,height=820');
    if (!ventana) {
      this.toast.error('Bloqueado', 'Permite ventanas emergentes para generar el reporte PDF');
      return;
    }
    ventana.document.write('<p style="font-family:sans-serif;padding:20px;">Generando reporte…</p>');

    this.reporteDocExportandoPdf.set(true);

    fetch('/assets/images/logo-dark.png')
      .then(r => r.blob())
      .then(blob => new Promise<string>(resolve => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result as string);
        reader.readAsDataURL(blob);
      }))
      .catch(() => '')
      .then((logoBase64) => {
        this.reporteDocExportandoPdf.set(false);

        const ahora = new Date();
        const fecha = ahora.toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });
        const hora  = ahora.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
        const fmtFecha = (f: string) => new Date(f + 'T12:00:00').toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' });

        const rangoLabel = this.reporteDocFechaInicio() && this.reporteDocFechaFin()
          ? `${fmtFecha(this.reporteDocFechaInicio())} al ${fmtFecha(this.reporteDocFechaFin())}`
          : 'Todo el histórico';

        const filasHtml = filas.map((e, idx) => `
          <tr class="${idx % 2 === 0 ? '' : 'alt'}">
            <td class="center">${fmtFecha(e.fecha_envio)}</td>
            <td>${e.curso_nombre ?? '—'}</td>
            <td class="bold">${e.participante_nombre}</td>
            <td>${e.participante_ciudad ?? '—'}</td>
            <td>${e.tipo_documento ?? '—'}</td>
          </tr>`).join('');

        const logoHtml = logoBase64
          ? `<img src="${logoBase64}" alt="CENEFCO" style="height:52px; object-fit:contain;" />`
          : `<div class="logo-fallback">CENEFCO</div>`;

        const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Envíos de Documentos — CENEFCO</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Arial', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
    .page-header { display: flex; align-items: stretch; border-bottom: 4px solid #1e3a5f; }
    .header-logo { padding: 16px 24px; display: flex; align-items: center; background: #fff; border-right: 1px solid #e2e8f0; }
    .logo-fallback { font-size: 22px; font-weight: 900; color: #1e3a5f; letter-spacing: -1px; }
    .header-info { flex: 1; padding: 12px 20px; background: #1e3a5f; color: #fff; display: flex; flex-direction: column; justify-content: center; }
    .header-info .report-title { font-size: 15px; font-weight: 700; letter-spacing: 0.3px; text-transform: uppercase; }
    .header-info .report-subtitle { font-size: 9px; color: #93c5fd; margin-top: 2px; }
    .header-meta { padding: 12px 20px; background: #f0f4f8; border-left: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: center; gap: 2px; text-align: right; min-width: 160px; }
    .header-meta .meta-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
    .header-meta .meta-val { font-size: 9px; color: #1e293b; font-weight: 600; }
    .band { height: 5px; background: linear-gradient(90deg, #1e3a5f 0%, #2563eb 50%, #38bdf8 100%); }
    .summary { display: flex; gap: 0; border-bottom: 1px solid #e2e8f0; }
    .summary-card { flex: 1; padding: 12px 18px; border-right: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; }
    .summary-card:last-child { border-right: none; }
    .summary-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .icon-periodo { background: #dbeafe; color: #1d4ed8; }
    .icon-total { background: #ede9fe; color: #6d28d9; }
    .summary-text .s-num { font-size: 18px; font-weight: 800; color: #0f172a; line-height: 1; }
    .summary-text .s-label { font-size: 8.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 1px; }
    .section-header { padding: 14px 20px 6px; display: flex; align-items: center; gap: 8px; }
    .section-line { flex: 1; height: 1px; background: #e2e8f0; }
    .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #475569; white-space: nowrap; }
    .table-wrap { padding: 0 20px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a5f; }
    thead th { padding: 8px 8px; text-align: left; font-size: 8.5px; font-weight: 700; color: #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    thead th.center { text-align: center; }
    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr.alt { background: #f8fafc; }
    tbody td { padding: 6.5px 8px; font-size: 9px; vertical-align: middle; color: #334155; }
    td.center { text-align: center; }
    td.bold { font-weight: 600; color: #0f172a; }
    .empty-msg { padding: 16px 20px; font-size: 9px; color: #94a3b8; text-align: center; }
    .page-footer { margin-top: 16px; padding: 10px 20px; border-top: 2px solid #1e3a5f; display: flex; align-items: center; justify-content: space-between; }
    .page-footer .footer-left { font-size: 8px; color: #64748b; }
    .page-footer .footer-right { font-size: 8px; color: #94a3b8; text-align: right; }
    .page-footer .footer-confidencial { font-size: 7.5px; color: #94a3b8; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-print-bar { position: fixed; bottom: 20px; right: 20px; display: flex; gap: 8px; z-index: 999; }
    .btn-print { padding: 10px 22px; background: #1e3a5f; color: #fff; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .btn-print:hover { background: #2563eb; }
    .btn-close { padding: 10px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; cursor: pointer; }
    @page { size: A4 landscape; margin: 12mm 10mm; }
    @media print {
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .btn-print-bar { display: none !important; }
      thead { display: table-header-group; }
      tbody tr { page-break-inside: avoid; }
    }
  </style>
</head>
<body>

  <div class="page-header">
    <div class="header-logo">${logoHtml}</div>
    <div class="header-info">
      <div class="report-title">Reporte de Envíos de Documentos</div>
      <div class="report-subtitle">Centro Nacional de Educación y Formación Continua — Unidad Académica</div>
    </div>
    <div class="header-meta">
      <span class="meta-label">Fecha de emisión</span>
      <span class="meta-val">${fecha}</span>
      <span class="meta-label" style="margin-top:6px;">Hora</span>
      <span class="meta-val">${hora}</span>
    </div>
  </div>

  <div class="band"></div>

  <div class="summary">
    <div class="summary-card">
      <div class="summary-icon icon-periodo">📅</div>
      <div class="summary-text">
        <div class="s-num" style="font-size:13px;">${rangoLabel}</div>
        <div class="s-label">Rango del reporte</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-total">📄</div>
      <div class="summary-text">
        <div class="s-num">${filas.length}</div>
        <div class="s-label">Total Envíos</div>
      </div>
    </div>
  </div>

  <div class="section-header">
    <span class="section-title">Detalle de Envíos</span>
    <div class="section-line"></div>
    <span style="font-size:8px; color:#94a3b8; white-space:nowrap; margin-left:8px;">${filas.length} registro(s)</span>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th class="center" style="width:70px;">Fecha</th>
          <th>Curso</th>
          <th>Participante</th>
          <th>Ciudad</th>
          <th>Documento</th>
        </tr>
      </thead>
      <tbody>${filasHtml}</tbody>
    </table>
  </div>

  <div class="page-footer">
    <div class="footer-left">
      <strong>CENEFCO</strong> — Centro Nacional de Educación y Formación Continua<br>
      Documento generado automáticamente por el Sistema de Gestión Académica
    </div>
    <div class="footer-confidencial">Documento de uso interno — Confidencial</div>
    <div class="footer-right">
      Emitido: ${fecha} · ${hora}<br>
      Total de envíos: ${filas.length}
    </div>
  </div>

  <div class="btn-print-bar">
    <button class="btn-close" onclick="window.close()">Cerrar</button>
    <button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button>
  </div>

  <script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
  </script>
</body>
</html>`;

        ventana.document.open();
        ventana.document.write(html);
        ventana.document.close();
      }).catch(() => {
        this.reporteDocExportandoPdf.set(false);
        ventana.close();
        this.toast.error('Error', 'No se pudo generar el reporte PDF');
      });
  }

  cuotasAbierto         = signal(false);
  cuotasPeriodo         = signal<PeriodoEstadisticas>('mes');
  cuotasFecha           = signal(this.hoyIso());
  cuotasFechaFin        = signal(this.hoyIso());
  cuotasConInactivos    = signal(false);
  cuotasCargando        = signal(false);
  cuotasError           = signal(false);
  cuotasDatos           = signal<ReporteCobrosResponse | null>(null);
  cuotasExportandoExcel = signal(false);
  cuotasExportandoPdf   = signal(false);

  cuotasFilas = computed<FilaCuota[]>(() => {
    const d = this.cuotasDatos();
    if (!d) return [];
    const filas: FilaCuota[] = [];
    for (const p of d.participantes) {
      if (p.cuotas.length > 0) {
        for (const c of p.cuotas) {
          filas.push({
            curso_nombre:       p.curso_nombre ?? '—',
            estudiante_nombre:  p.estudiante_nombre,
            estudiante_ci:      p.estudiante_ci,
            estudiante_email:   p.estudiante_email,
            estudiante_celular: p.estudiante_celular,
            cuota_nro:          c.cuota_nro,
            cuota_monto:        c.cuota_monto,
            monto_pagado:       c.monto_pagado,
            estado:             c.estado_cuota,
            fecha_limite:       c.cuota_fecha_fin,
            dias_atraso:        c.dias_atraso,
          });
        }
        continue;
      }

      if (p.pagos.length > 0) {
        for (const pago of p.pagos) {
          filas.push({
            curso_nombre:       p.curso_nombre ?? '—',
            estudiante_nombre:  p.estudiante_nombre,
            estudiante_ci:      p.estudiante_ci,
            estudiante_email:   p.estudiante_email,
            estudiante_celular: p.estudiante_celular,
            cuota_nro:          pago.cuota_nro ?? (pago.es_anticipo ? 'Anticipo' : null),
            cuota_monto:        null,
            monto_pagado:       pago.monto_pagado,
            estado:             'pagada',
            fecha_limite:       pago.fecha_deposito,
            dias_atraso:        0,
          });
        }
        continue;
      }

      filas.push({
        curso_nombre:       p.curso_nombre ?? '—',
        estudiante_nombre:  p.estudiante_nombre,
        estudiante_ci:      p.estudiante_ci,
        estudiante_email:   p.estudiante_email,
        estudiante_celular: p.estudiante_celular,
        cuota_nro:          null,
        cuota_monto:        p.plan_no_asignado ? null : p.total_plan,
        monto_pagado:       p.total_pagado,
        estado:             p.estado_general,
        fecha_limite:       null,
        dias_atraso:        0,
      });
    }
    return filas;
  });

  cuotasFiltro = signal<'todos' | 'sin_pagar'>('todos');

  cuotasFilasFiltradas = computed<FilaCuota[]>(() => {
    const filas = this.cuotasFilas();
    if (this.cuotasFiltro() === 'todos') return filas;
    return filas.filter(f => f.monto_pagado === 0);
  });

  abrirReporteCuotas(): void {
    this.cuotasAbierto.set(true);
    this.cuotasFiltro.set('todos');
    this.buscarReporteCuotas();
  }

  cerrarReporteCuotas(): void {
    this.cuotasAbierto.set(false);
  }

  cambiarPeriodoCuotas(periodo: PeriodoEstadisticas): void {
    this.cuotasPeriodo.set(periodo);
    if (periodo === 'rango' && this.cuotasFechaFin() < this.cuotasFecha()) {
      this.cuotasFechaFin.set(this.cuotasFecha());
    }
    this.buscarReporteCuotas();
  }

  onCuotasFechaInput(event: Event, periodo: PeriodoEstadisticas): void {
    const valor = (event.target as HTMLInputElement).value;
    if (!valor) return;

    if (periodo === 'mes') {
      this.cuotasFecha.set(`${valor}-01`);
    } else if (periodo === 'anio') {
      this.cuotasFecha.set(`${valor}-01-01`);
    } else {
      this.cuotasFecha.set(valor);
      if (periodo === 'rango' && this.cuotasFechaFin() < valor) {
        this.cuotasFechaFin.set(valor);
      }
    }
    this.buscarReporteCuotas();
  }

  onCuotasFechaFinInput(event: Event): void {
    const valor = (event.target as HTMLInputElement).value;
    if (!valor) return;
    this.cuotasFechaFin.set(valor);
    if (valor < this.cuotasFecha()) {
      this.cuotasFecha.set(valor);
    }
    this.buscarReporteCuotas();
  }

  get cuotasFechaMes(): string { return this.cuotasFecha().slice(0, 7); }
  get cuotasFechaAnio(): string { return this.cuotasFecha().slice(0, 4); }

  toggleCuotasConInactivos(): void {
    this.cuotasConInactivos.update(v => !v);
    this.buscarReporteCuotas();
  }

  cambiarCuotasFiltro(filtro: 'todos' | 'sin_pagar'): void {
    this.cuotasFiltro.set(filtro);
    this.buscarReporteCuotas();
  }

  buscarReporteCuotas(): void {
    this.cuotasCargando.set(true);
    this.cuotasError.set(false);

    const sinFiltroFecha = this.cuotasFiltro() === 'sin_pagar';

    this.reporteCobrosService.getReporte({
      periodo:       sinFiltroFecha ? undefined : this.cuotasPeriodo(),
      fecha:         sinFiltroFecha ? undefined : this.cuotasFecha(),
      fecha_fin:     (!sinFiltroFecha && this.cuotasPeriodo() === 'rango') ? this.cuotasFechaFin() : undefined,
      con_inactivos: this.cuotasConInactivos(),
    }).subscribe({
      next: (r) => {
        this.cuotasDatos.set(r);
        this.cuotasCargando.set(false);
      },
      error: () => {
        this.cuotasDatos.set(null);
        this.cuotasCargando.set(false);
        this.cuotasError.set(true);
      }
    });
  }

  cuotaEstadoLabel(e: string): string { return CUOTA_ESTADO_LABELS[e] ?? e; }

  cuotaEstadoClase(e: string): string {
    const clases: Record<string, string> = {
      pagada: 'bg-success/10 text-success', completo: 'bg-success/10 text-success',
      parcial: 'bg-amber-100 text-amber-700', al_dia: 'bg-blue-100 text-blue-700',
      vencida: 'bg-danger/10 text-danger', en_mora: 'bg-danger/10 text-danger',
      pendiente: 'bg-default-100 text-default-500', sin_pagos: 'bg-default-200 text-default-600',
      sin_plan: 'bg-amber-100 text-amber-700',
    };
    return clases[e] ?? 'bg-default-100 text-default-500';
  }

  formatFechaCuota(f: string | null): string {
    if (!f) return '—';
    try {
      return new Date(f + 'T00:00:00').toLocaleDateString('es-BO', { day: '2-digit', month: '2-digit', year: 'numeric' });
    } catch { return f; }
  }

  private nombreArchivoCuotas(ext: string): string {
    const fecha  = new Date().toISOString().slice(0, 10);
    const sufijo = this.cuotasFiltro() === 'sin_pagar' ? '-sin-pagar' : '';
    return `reporte-cuotas-todos-los-cursos${sufijo}-${fecha}.${ext}`;
  }

  exportarReporteCuotasExcel(): void {
    const filas = this.cuotasFilasFiltradas();
    const d = this.cuotasDatos();
    if (!filas.length || !d) {
      this.toast.warning('Sin datos', 'No hay cuotas para exportar');
      return;
    }

    this.cuotasExportandoExcel.set(true);

    const wb = XLSX.utils.book_new();

    const resumen: (string | number)[][] = [
      ['Métrica', 'Valor'],
      ['Filtro', this.cuotasFiltro() === 'sin_pagar' ? 'Solo sin pagar' : 'Todos'],
      ['Total participantes', d.totales.total_participantes],
      ['Al día', d.totales.al_dia],
      ['En mora', d.totales.en_mora],
      ['Completo', d.totales.completo],
      ['Sin pagos', d.totales.sin_pagos],
      ['Sin plan de pagos', d.totales.sin_plan],
      ['Total esperado (Bs.)', d.totales.total_plan],
      ['Total cobrado (Bs.)', d.totales.total_pagado],
      ['Total pendiente (Bs.)', d.totales.pendiente],
    ];
    const wsResumen = XLSX.utils.aoa_to_sheet(resumen);
    wsResumen['!cols'] = [{ wch: 24 }, { wch: 20 }];
    XLSX.utils.book_append_sheet(wb, wsResumen, 'Resumen');

    const filasHoja = filas.map(f => ({
      'Curso':              f.curso_nombre,
      'Estudiante':         f.estudiante_nombre,
      'CI':                 f.estudiante_ci ?? '',
      'Email':              f.estudiante_email ?? '',
      'Celular':            f.estudiante_celular ?? '',
      'Cuota':              f.cuota_nro ?? '',
      'Monto cuota (Bs.)':  f.cuota_monto ?? '',
      'Monto pagado (Bs.)': f.monto_pagado,
      'Estado':             this.cuotaEstadoLabel(f.estado),
      'Fecha':              f.fecha_limite ?? '',
      'Días de atraso':     f.dias_atraso,
    }));
    const wsDetalle = XLSX.utils.json_to_sheet(filasHoja);
    wsDetalle['!cols'] = [
      { wch: 28 }, { wch: 26 }, { wch: 12 }, { wch: 26 }, { wch: 14 },
      { wch: 10 }, { wch: 16 }, { wch: 16 }, { wch: 14 }, { wch: 14 }, { wch: 14 },
    ];
    XLSX.utils.book_append_sheet(wb, wsDetalle, 'Detalle por cuota');

    XLSX.writeFile(wb, this.nombreArchivoCuotas('xlsx'));
    this.cuotasExportandoExcel.set(false);
  }

  async exportarReporteCuotasPDF(): Promise<void> {
    const filas = this.cuotasFilasFiltradas();
    const d = this.cuotasDatos();
    if (!filas.length || !d) {
      this.toast.warning('Sin datos', 'No hay cuotas para exportar');
      return;
    }

    this.cuotasExportandoPdf.set(true);
    try {
      const { jsPDF } = await import('jspdf');
      const autoTable  = (await import('jspdf-autotable')).default;

      const doc   = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
      const pw    = doc.internal.pageSize.getWidth();
      const fecha = new Date().toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });

      doc.setFillColor(7, 67, 91);
      doc.rect(0, 0, pw, 22, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(14);
      doc.setFont('helvetica', 'bold');
      const titulo = this.cuotasFiltro() === 'sin_pagar'
        ? 'CENEFCO — Reporte de Cuotas Sin Pagar (Todos los Cursos)'
        : 'CENEFCO — Reporte de Cuotas (Todos los Cursos)';
      doc.text(titulo, 10, 10);
      doc.setFontSize(9);
      doc.setFont('helvetica', 'normal');
      doc.text(`Generado: ${fecha}`, pw - 10, 10, { align: 'right' });

      autoTable(doc, {
        startY: 28,
        head: [['Total part.', 'Al día', 'En mora', 'Completo', 'Sin pagos', 'Sin plan', 'Esperado (Bs.)', 'Cobrado (Bs.)', 'Pendiente (Bs.)']],
        body: [[
          d.totales.total_participantes, d.totales.al_dia, d.totales.en_mora, d.totales.completo,
          d.totales.sin_pagos, d.totales.sin_plan,
          d.totales.total_plan.toFixed(2), d.totales.total_pagado.toFixed(2), d.totales.pendiente.toFixed(2),
        ]],
        theme: 'plain',
        headStyles: { fillColor: [240, 240, 240], textColor: [40, 40, 40], fontStyle: 'bold' },
        styles: { fontSize: 8 },
      });

      autoTable(doc, {
        startY: (doc as any).lastAutoTable.finalY + 6,
        head: [['Curso', 'Estudiante', 'CI', 'Celular', 'Cuota', 'Monto', 'Pagado', 'Estado', 'Fecha', 'Atraso']],
        body: filas.map(f => [
          f.curso_nombre, f.estudiante_nombre, f.estudiante_ci ?? '—', f.estudiante_celular ?? '—', f.cuota_nro ?? '—',
          f.cuota_monto != null ? f.cuota_monto.toFixed(2) : '—',
          f.monto_pagado.toFixed(2),
          this.cuotaEstadoLabel(f.estado),
          this.formatFechaCuota(f.fecha_limite),
          f.dias_atraso > 0 ? `${f.dias_atraso} d.` : '—',
        ]),
        theme: 'striped',
        headStyles: { fillColor: [7, 67, 91] },
        styles: { fontSize: 7, cellPadding: 1.5 },
        columnStyles: { 5: { halign: 'right' }, 6: { halign: 'right' } },
        didDrawPage: () => {
          const ph = doc.internal.pageSize.getHeight();
          doc.setFontSize(7);
          doc.setTextColor(140);
          doc.text(`Página ${doc.getNumberOfPages()}`, pw - 10, ph - 6, { align: 'right' });
        },
      });

      doc.save(this.nombreArchivoCuotas('pdf'));
    } finally {
      this.cuotasExportandoPdf.set(false);
    }
  }

  planCobrosCurso          = signal<Curso | null>(null);
  planCobrosData           = signal<PlanCobrosCurso | null>(null);
  planCobrosCargando       = signal(false);
  planCobrosError          = signal(false);
  planCobrosCuotas         = signal<FechaPago[]>([]);
  planCobrosCuotasCargando = signal(false);
  planCobrosEliminandoId   = signal<number | null>(null);

  abrirPlanCobros(curso: Curso): void {
    if (!curso.id_imp) {
      this.toast.error('Sin apertura', 'Este curso no tiene una apertura (id_imp) asignada. Configúrala primero.');
      return;
    }

    this.planCobrosCurso.set(curso);
    this.planCobrosData.set(null);
    this.planCobrosCuotas.set([]);
    this.planCobrosError.set(false);
    this.planCobrosCargando.set(true);

    this.cursoService.obtenerOCrearPlanCobros(curso.id_programa).subscribe({
      next: (plan) => {
        this.planCobrosData.set(plan);
        this.planCobrosCargando.set(false);
        this.cargarCuotasPlan(plan.id_plan);
      },
      error: () => {
        this.planCobrosCargando.set(false);
        this.planCobrosError.set(true);
      }
    });
  }

  cerrarPlanCobros(): void {
    this.planCobrosCurso.set(null);
  }

  cargarCuotasPlan(idPlan: number): void {
    this.planCobrosCuotasCargando.set(true);
    this.fechaPagoService.getAll({ id_plan: idPlan, pageSize: 100 }).subscribe({
      next: (r) => {
        this.planCobrosCuotas.set(r.data);
        this.planCobrosCuotasCargando.set(false);
      },
      error: () => {
        this.planCobrosCuotas.set([]);
        this.planCobrosCuotasCargando.set(false);
      }
    });
  }

  private refrescarAlertasCobros(): void {
    this.cursoService.getAlertasCobros().subscribe({
      next: (alertas) => this.alertasCobros.set(new Map(alertas.map(a => [a.id_imp, a]))),
      error: () => {},
    });
  }

  onCuotasGeneradas(): void {
    const plan = this.planCobrosData();
    if (plan) this.cargarCuotasPlan(plan.id_plan);
    this.refrescarAlertasCobros();
  }

  eliminarCuotaPlan(cuota: FechaPago): void {
    Swal.fire({
      title: '¿Eliminar cuota?',
      text: `Cuota ${cuota.nro_pago ?? ''} — Bs. ${cuota.monto_a_pagar ?? 0}`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (!result.isConfirmed) return;
      this.planCobrosEliminandoId.set(cuota.id_fechapago);
      this.fechaPagoService.delete(cuota.id_fechapago).subscribe({
        next: () => {
          this.planCobrosEliminandoId.set(null);
          const plan = this.planCobrosData();
          if (plan) this.cargarCuotasPlan(plan.id_plan);
          this.refrescarAlertasCobros();
        },
        error: () => {
          this.planCobrosEliminandoId.set(null);
          this.toast.error('Error', 'No se pudo eliminar la cuota');
        }
      });
    });
  }

  planCobrosEditandoId      = signal<number | null>(null);
  editCuotaNroPago          = signal('');
  editCuotaMonto            = signal<number | null>(null);
  editCuotaFechaInicio      = signal('');
  editCuotaFechaFin         = signal('');
  planCobrosGuardandoEdicion = signal(false);

  iniciarEdicionCuota(cuota: FechaPago): void {
    this.planCobrosEditandoId.set(cuota.id_fechapago);
    this.editCuotaNroPago.set(cuota.nro_pago ?? '');
    this.editCuotaMonto.set(cuota.monto_a_pagar);
    this.editCuotaFechaInicio.set(cuota.fecha_inicio ?? '');
    this.editCuotaFechaFin.set(cuota.fecha_fin ?? '');
  }

  cancelarEdicionCuota(): void {
    this.planCobrosEditandoId.set(null);
  }

  guardarEdicionCuota(): void {
    const id = this.planCobrosEditandoId();
    if (!id) return;

    const monto = this.editCuotaMonto();
    if (!monto || monto <= 0) { this.toast.error('Error', 'Ingrese un monto válido'); return; }
    if (!this.editCuotaFechaFin()) { this.toast.error('Error', 'Seleccione la fecha de vencimiento'); return; }

    this.planCobrosGuardandoEdicion.set(true);
    this.fechaPagoService.update(id, {
      nro_pago:      this.editCuotaNroPago() || null,
      monto_a_pagar: monto,
      fecha_inicio:  this.editCuotaFechaInicio() || null,
      fecha_fin:     this.editCuotaFechaFin(),
    }).subscribe({
      next: () => {
        this.planCobrosGuardandoEdicion.set(false);
        this.planCobrosEditandoId.set(null);
        const plan = this.planCobrosData();
        if (plan) this.cargarCuotasPlan(plan.id_plan);
        this.refrescarAlertasCobros();
        this.toast.success('Guardado', 'Cuota actualizada correctamente');
      },
      error: () => {
        this.planCobrosGuardandoEdicion.set(false);
        this.toast.error('Error', 'No se pudo actualizar la cuota');
      }
    });
  }

  formatFechaPlanCobros(f: string | null): string {
    if (!f) return '—';
    try {
      return new Date(f + 'T00:00:00').toLocaleDateString('es-BO', { day: '2-digit', month: '2-digit', year: 'numeric' });
    } catch { return f; }
  }

  qrCurso     = signal<Curso | null>(null);
  qrDataUrl   = signal<string | null>(null);
  qrUrl       = signal<string | null>(null);
  qrTitulo    = signal<string>('');
  qrSubtitulo = signal<string>('');
  qrCopiado   = signal(false);

  urlParticipantes(curso: Curso): string {
    return curso.slug ? `${portalUrl}/cursos/${curso.slug}-participantes` : portalUrl;
  }

  async abrirQR(curso: Curso): Promise<void> {
    const url = curso.slug ? `${portalUrl}/programas/${curso.slug}` : `${portalUrl}/programas`;
    await this.generarQR(curso, url, 'Escanea el código para abrir la página del programa', 'qr');
  }

  async abrirQRParticipantes(curso: Curso): Promise<void> {
    const url = this.urlParticipantes(curso);
    await this.generarQR(curso, url, 'Escanea el código para ver la lista de aprobados', 'qr-aprobados');
  }

  private async generarQR(curso: Curso, url: string, subtitulo: string, prefijoArchivo: string): Promise<void> {
    const dataUrl = await QRCode.toDataURL(url, { width: 300, margin: 2, color: { dark: '#111827', light: '#ffffff' } });
    this.qrCurso.set(curso);
    this.qrDataUrl.set(dataUrl);
    this.qrUrl.set(url);
    this.qrTitulo.set(`${prefijoArchivo}-${curso.nombre_programa}`);
    this.qrSubtitulo.set(subtitulo);
    this.qrCopiado.set(false);
  }

  cerrarQR(): void {
    this.qrCurso.set(null);
    this.qrDataUrl.set(null);
    this.qrUrl.set(null);
  }

  descargarQR(): void {
    const url    = this.qrDataUrl();
    const nombre = this.qrTitulo() || 'qr';
    if (!url) return;
    const a = document.createElement('a');
    a.href = url;
    a.download = `${nombre.toLowerCase().replace(/\s+/g, '-')}.png`;
    a.click();
  }

  async copiarUrlQR(): Promise<void> {
    const url = this.qrUrl();
    if (!url) return;
    await navigator.clipboard.writeText(url);
    this.qrCopiado.set(true);
    setTimeout(() => this.qrCopiado.set(false), 2000);
  }

  sincronizandoId = signal<number | null>(null);

  sincronizarMoodle(curso: Curso): void {
    Swal.fire({
      title: '¿Crear en Moodle?',
      html: `Se creará el curso <b>${curso.nombre_programa}</b> en Moodle.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#0ea5e9',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, crear',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (!result.isConfirmed) return;
      this.sincronizandoId.set(curso.id_programa);
      this.cursoService.sincronizarMoodle(curso.id_programa).subscribe({
        next: (moodleCurso) => {
          this.sincronizandoId.set(null);
          this.toast.success('¡Creado en Moodle!', `Curso creado con ID ${moodleCurso.id}`);
        },
        error: () => {
          this.sincronizandoId.set(null);
          this.toast.error('Error', 'No se pudo crear el curso en Moodle');
        }
      });
    });
  }

  exportandoMoodleId = signal<number | null>(null);

  exportarMoodleCSV(curso: Curso): void {
    if (!curso.id_imp) return;

    this.exportandoMoodleId.set(curso.id_imp);
    this.cursoService.exportarMoodleCSV(curso.id_imp).subscribe({
      next: (blob) => {
        this.exportandoMoodleId.set(null);
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `moodle-usuarios-${curso.slug || curso.id_imp}.csv`;
        a.click();
        URL.revokeObjectURL(url);
        this.toast.success('CSV generado', 'Súbelo en Moodle desde Administración del sitio › Usuarios › Cargar usuarios.');
      },
      error: () => {
        this.exportandoMoodleId.set(null);
        this.toast.error('Error', 'No se pudo generar el CSV de usuarios');
      }
    });
  }

  grabacionesModal    = signal(false);
  todasGrabaciones    = signal<ZoomRecording[]>([]);
  grabacionesCargando = signal(false);

  abrirTodasGrabaciones(): void {
    this.grabacionesModal.set(true);
    this.todasGrabaciones.set([]);
    this.grabacionesCargando.set(true);
    this.zoomService.grabaciones().subscribe({
      next: (res) => {
        this.todasGrabaciones.set(res.recordings);
        this.grabacionesCargando.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar las grabaciones de Zoom');
        this.grabacionesCargando.set(false);
      }
    });
  }

  cerrarTodasGrabaciones(): void {
    this.grabacionesModal.set(false);
  }

  recargarGrabaciones(): void {
    this.todasGrabaciones.set([]);
    this.grabacionesCargando.set(true);
    this.zoomService.grabaciones().subscribe({
      next: (res) => {
        this.todasGrabaciones.set(res.recordings);
        this.grabacionesCargando.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar las grabaciones');
        this.grabacionesCargando.set(false);
      }
    });
  }

  exportarCSV(): void {
    const rows = this.todasGrabaciones();
    if (!rows.length) return;

    const encabezados = ['Curso', 'Fecha', 'Duración (min)', 'Tipo', 'Tamaño (MB)', 'Link Play', 'Link Descarga'];
    const lineas = rows.map(r => [
      `"${(r.curso ?? '').replace(/"/g, '""')}"`,
      `"${r.fecha ?? ''}"`,
      r.duracion_min,
      r.tipo_archivo ?? '',
      r.tamanio_mb,
      r.link_play ?? '',
      r.link_descarga ?? '',
    ].join(','));

    const csv = '﻿' + encabezados.join(',') + '\n' + lineas.join('\n');
    this.descargarArchivo(csv, 'zoom_grabaciones.csv', 'text/csv;charset=utf-8;');
  }

  exportarExcel(): void {
    const rows = this.todasGrabaciones();
    if (!rows.length) return;

    const datos = rows.map(r => ({
      'Curso':           r.curso ?? '',
      'Fecha':           r.fecha ?? '',
      'Duración (min)':  r.duracion_min,
      'Tipo':            r.tipo_archivo ?? '',
      'Tamaño (MB)':     r.tamanio_mb,
      'Link Play':       r.link_play ?? '',
      'Link Descarga':   r.link_descarga ?? '',
    }));

    const hoja  = XLSX.utils.json_to_sheet(datos);
    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, 'Grabaciones');
    XLSX.writeFile(libro, 'zoom_grabaciones.xlsx');
  }

  private descargarArchivo(contenido: string, nombre: string, tipo: string): void {
    const blob = new Blob([contenido], { type: tipo });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = nombre;
    a.click();
    URL.revokeObjectURL(url);
  }

  zoomCurso      = signal<Curso | null>(null);
  zoomVista      = signal<ZoomVista>('reuniones');
  zoomReuniones  = signal<ZoomMeeting[]>([]);
  zoomGrabaciones = signal<ZoomRecording[]>([]);
  zoomCargando   = signal(false);
  zoomCreando    = signal(false);

  zoomTipo      = signal<'unica' | 'multisesion'>('unica');
  zoomTema      = signal('');
  zoomFecha     = signal('');
  zoomDuracion  = signal(60);
  zoomSesiones  = signal(2);
  zoomDias      = signal(7);

  abrirZoom(curso: Curso): void {
    this.zoomCurso.set(curso);
    this.zoomVista.set('reuniones');
    this.zoomTema.set(curso.nombre_programa);
    this.zoomTipo.set('unica');
    this.zoomFecha.set('');
    this.zoomReuniones.set([]);
    this.zoomGrabaciones.set([]);
    this.cargarReuniones();
  }

  cerrarZoom(): void {
    this.zoomCurso.set(null);
  }

  cambiarVistaZoom(vista: ZoomVista): void {
    this.zoomVista.set(vista);
    if (vista === 'grabaciones' && this.zoomGrabaciones().length === 0) {
      this.cargarGrabaciones();
    }
    if (vista === 'reuniones' && this.zoomReuniones().length === 0) {
      this.cargarReuniones();
    }
  }

  cargarReuniones(): void {
    this.zoomCargando.set(true);
    this.zoomService.listarReuniones().subscribe({
      next: (res) => {
        this.zoomReuniones.set(res.meetings);
        this.zoomCargando.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar las reuniones de Zoom');
        this.zoomCargando.set(false);
      }
    });
  }

  cargarGrabaciones(): void {
    this.zoomCargando.set(true);
    this.zoomService.grabaciones().subscribe({
      next: (res) => {
        this.zoomGrabaciones.set(res.recordings);
        this.zoomCargando.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar las grabaciones');
        this.zoomCargando.set(false);
      }
    });
  }

  crearReunionZoom(): void {
    if (!this.zoomFecha()) {
      this.toast.error('Formulario incompleto', 'Debes ingresar la fecha y hora de inicio');
      return;
    }

    this.zoomCreando.set(true);

    const payload = this.zoomTipo() === 'unica'
      ? {
          tipo: 'unica' as const,
          tema: this.zoomTema(),
          fecha_inicio: this.zoomFecha(),
          duracion_min: this.zoomDuracion(),
        }
      : {
          tipo: 'multisesion' as const,
          curso: this.zoomTema(),
          fecha_inicio: this.zoomFecha(),
          duracion_min: this.zoomDuracion(),
          n_sesiones: this.zoomSesiones(),
          dias_entre: this.zoomDias(),
        };

    this.zoomService.crearReunion(payload).subscribe({
      next: (res) => {
        this.zoomCreando.set(false);
        if (res.tipo === 'unica' && res.reunion) {
          this.toast.success('¡Reunión creada en Zoom!', `ID: ${res.reunion.id} | Contraseña: ${res.reunion.password}`);
        } else if (res.sesiones) {
          this.toast.success('¡Sesiones creadas en Zoom!', `${res.sesiones.length} sesiones programadas`);
        }
        this.zoomVista.set('reuniones');
        this.zoomReuniones.set([]);
        this.cargarReuniones();
      },
      error: () => {
        this.zoomCreando.set(false);
        this.toast.error('Error', 'No se pudo crear la reunión en Zoom');
      }
    });
  }

  formatFechaZoom(fecha: string): string {
    if (!fecha) return '—';
    return new Date(fecha).toLocaleString('es-BO', {
      dateStyle: 'short',
      timeStyle: 'short',
    });
  }

  certCurso       = signal<Curso | null>(null);
  certPlantillas  = signal<CertPlantilla[]>([]);
  certPlantillaId = signal<number | null>(null);
  certPreview     = signal<{ id_imp: number; total: number; estudiantes: { id_us: number; nombre: string }[] } | null>(null);
  certCargando    = signal(false);
  certGenerando   = signal(false);
  certResultado   = signal<GenerarLoteResult | null>(null);
  certDescargandoZip = signal(false);

  abrirCertificados(curso: Curso): void {
    if (!curso.id_imp) {
      this.toast.error('Sin impartición', 'Este curso no tiene una apertura (id_imp) asignada. Configúrala primero.');
      return;
    }
    this.certCurso.set(curso);
    this.certPlantillaId.set(null);
    this.certPreview.set(null);
    this.certResultado.set(null);
    this.certCargando.set(true);

    this.certService.getPlantillas({ soloActivos: true, pageSize: 100 }).subscribe({
      next: r => this.certPlantillas.set(r.data),
      error: () => this.certPlantillas.set([]),
    });

    this.certService.previewPagosCompletos(curso.id_programa).subscribe({
      next: preview => { this.certPreview.set(preview); this.certCargando.set(false); },
      error: () => { this.certCargando.set(false); this.toast.error('Error', 'No se pudo cargar la vista previa'); },
    });
  }

  cerrarCertificados(): void {
    this.certCurso.set(null);
    this.certResultado.set(null);
  }

  generarCertificados(): void {
    const curso      = this.certCurso();
    const plantillaId = this.certPlantillaId();
    if (!curso || !plantillaId) { this.toast.error('Error', 'Seleccione una plantilla'); return; }

    this.certGenerando.set(true);
    this.certService.generarPagosCompletos(curso.id_programa, plantillaId).subscribe({
      next: resultado => {
        this.certGenerando.set(false);
        this.certResultado.set(resultado);
        if (resultado.generados > 0) {
          this.descargarZipCertificados();
        } else {
          this.toast.warning('Sin certificados nuevos', 'No se generó ningún certificado nuevo en este lote.');
        }
      },
      error: () => {
        this.certGenerando.set(false);
        this.toast.error('Error', 'No se pudieron generar los certificados');
      },
    });
  }

  descargarZipCertificados(): void {
    const idImp = this.certPreview()?.id_imp;
    if (!idImp) return;
    this.certDescargandoZip.set(true);
    this.certService.descargarZip(idImp).subscribe({
      next: blob => {
        this.certDescargandoZip.set(false);
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `certificados-${(this.certCurso()?.nombre_programa ?? 'lote').toLowerCase().replace(/\s+/g, '-')}.zip`;
        a.click();
        URL.revokeObjectURL(url);
      },
      error: (err) => {
        this.certDescargandoZip.set(false);
        if (err?.status === 404) {
          this.toast.error('Sin certificados', 'No hay certificados generados para descargar');
        } else {
          this.toast.error('Error', 'No se pudo descargar el ZIP');
        }
      },
    });
  }

  descargarCertificadoIndividual(cert: { archivo_url: string | null; nombre_en_certificado: string; codigo_verificacion: string }): void {
    if (!cert.archivo_url) return;
    const a = document.createElement('a');
    a.href = cert.archivo_url;
    a.target = '_blank';
    a.rel = 'noopener';
    a.click();
  }

  generandoPDF = signal(false);

  generarReportePDF(): void {
    this.generandoPDF.set(true);

    const logoPromise = fetch('/assets/images/logo-dark.png')
      .then(r => r.blob())
      .then(blob => new Promise<string>(resolve => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result as string);
        reader.readAsDataURL(blob);
      }))
      .catch(() => '');

    Promise.all([
      firstValueFrom(this.cursoService.getAll({ pageSize: 9999 })),
      logoPromise,
    ]).then(([res, logoBase64]) => {
      this.generandoPDF.set(false);
      const cursos = res?.data ?? [];
      const ahora  = new Date();
      const fecha  = ahora.toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });
      const hora   = ahora.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });

      const fmt = (d: string | null) => d
        ? new Date(d + 'T12:00:00').toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' })
        : '—';

      const total       = cursos.length;
      const publicados  = cursos.filter(c => c.estado_web === 'publicado').length;
      const borradores  = cursos.filter(c => c.estado_web !== 'publicado').length;
      const destacados  = cursos.filter(c => c.destacado).length;

      const filas = cursos.map((c, i) => `
        <tr class="${i % 2 === 0 ? '' : 'alt'}">
          <td class="center">${c.id_programa}</td>
          <td class="bold">${c.nombre_programa}</td>
          <td>${c.categoria_nombre ?? '—'}</td>
          <td>${c.tipo_nombre ?? '—'}</td>
          <td class="center">${fmt(c.inicio_actividades)}</td>
          <td class="center">${fmt(c.finalizacion_actividades)}</td>
          <td class="center">
            <span class="${c.estado_web === 'publicado' ? 'badge-pub' : 'badge-bor'}">
              ${c.estado_web === 'publicado' ? 'Publicado' : 'Borrador'}
            </span>
          </td>
          <td class="center">${c.destacado ? '★' : ''}</td>
        </tr>`).join('');

      const logoHtml = logoBase64
        ? `<img src="${logoBase64}" alt="CENEFCO" style="height:52px; object-fit:contain;" />`
        : `<div class="logo-fallback">CENEFCO</div>`;

      const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Cursos y Programas — CENEFCO</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Arial', sans-serif;
      font-size: 10px;
      color: #1e293b;
      background: #fff;
    }

    .page-header {
      display: flex;
      align-items: stretch;
      border-bottom: 4px solid #1e3a5f;
    }
    .header-logo {
      padding: 16px 24px;
      display: flex;
      align-items: center;
      background: #fff;
      border-right: 1px solid #e2e8f0;
    }
    .logo-fallback {
      font-size: 22px;
      font-weight: 900;
      color: #1e3a5f;
      letter-spacing: -1px;
    }
    .header-info {
      flex: 1;
      padding: 12px 20px;
      background: #1e3a5f;
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .header-info .report-title {
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.3px;
      text-transform: uppercase;
    }
    .header-info .report-subtitle {
      font-size: 9px;
      color: #93c5fd;
      margin-top: 2px;
    }
    .header-meta {
      padding: 12px 20px;
      background: #f0f4f8;
      border-left: 1px solid #e2e8f0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 2px;
      text-align: right;
      min-width: 160px;
    }
    .header-meta .meta-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
    .header-meta .meta-val   { font-size: 9px; color: #1e293b; font-weight: 600; }

    .band {
      height: 5px;
      background: linear-gradient(90deg, #1e3a5f 0%, #2563eb 50%, #38bdf8 100%);
    }

    .summary {
      display: flex;
      gap: 0;
      border-bottom: 1px solid #e2e8f0;
    }
    .summary-card {
      flex: 1;
      padding: 12px 18px;
      border-right: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .summary-card:last-child { border-right: none; }
    .summary-icon {
      width: 34px;
      height: 34px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      flex-shrink: 0;
    }
    .icon-total    { background: #dbeafe; color: #1d4ed8; }
    .icon-pub      { background: #dcfce7; color: #15803d; }
    .icon-bor      { background: #f3f4f6; color: #6b7280; }
    .icon-dest     { background: #fef9c3; color: #b45309; }
    .summary-text .s-num   { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1; }
    .summary-text .s-label { font-size: 8.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 1px; }

    .section-header {
      padding: 10px 20px 6px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .section-line {
      flex: 1;
      height: 1px;
      background: #e2e8f0;
    }
    .section-title {
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #475569;
      white-space: nowrap;
    }

    .table-wrap { padding: 0 20px; }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    thead tr {
      background: #1e3a5f;
    }
    thead th {
      padding: 8px 8px;
      text-align: left;
      font-size: 8.5px;
      font-weight: 700;
      color: #e2e8f0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }
    thead th.center { text-align: center; }

    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr.alt { background: #f8fafc; }
    tbody td {
      padding: 6.5px 8px;
      font-size: 9px;
      vertical-align: middle;
      color: #334155;
    }
    td.center { text-align: center; }
    td.bold   { font-weight: 600; color: #0f172a; }

    .badge-pub {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 12px;
      background: #dcfce7;
      color: #15803d;
      font-size: 7.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .badge-bor {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 12px;
      background: #f1f5f9;
      color: #94a3b8;
      font-size: 7.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .page-footer {
      margin-top: 16px;
      padding: 10px 20px;
      border-top: 2px solid #1e3a5f;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .page-footer .footer-left { font-size: 8px; color: #64748b; }
    .page-footer .footer-right { font-size: 8px; color: #94a3b8; text-align: right; }
    .page-footer .footer-confidencial {
      font-size: 7.5px;
      color: #94a3b8;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .btn-print-bar {
      position: fixed;
      bottom: 20px;
      right: 20px;
      display: flex;
      gap: 8px;
      z-index: 999;
    }
    .btn-print {
      padding: 10px 22px;
      background: #1e3a5f;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .btn-print:hover { background: #2563eb; }
    .btn-close {
      padding: 10px 16px;
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 12px;
      cursor: pointer;
    }

    @page { size: A4 landscape; margin: 12mm 10mm; }
    @media print {
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .btn-print-bar { display: none !important; }
      thead { display: table-header-group; }
      tbody tr { page-break-inside: avoid; }
    }
  </style>
</head>
<body>

  <div class="page-header">
    <div class="header-logo">
      ${logoHtml}
    </div>
    <div class="header-info">
      <div class="report-title">Reporte de Cursos y Programas Académicos</div>
      <div class="report-subtitle">Centro Nacional de Educación y Formación Continua — Unidad Académica</div>
    </div>
    <div class="header-meta">
      <span class="meta-label">Fecha de emisión</span>
      <span class="meta-val">${fecha}</span>
      <span class="meta-label" style="margin-top:6px;">Hora</span>
      <span class="meta-val">${hora}</span>
    </div>
  </div>

  <div class="band"></div>

  <div class="summary">
    <div class="summary-card">
      <div class="summary-icon icon-total">📋</div>
      <div class="summary-text">
        <div class="s-num">${total}</div>
        <div class="s-label">Total Programas</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-pub">✓</div>
      <div class="summary-text">
        <div class="s-num">${publicados}</div>
        <div class="s-label">Publicados</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-bor">○</div>
      <div class="summary-text">
        <div class="s-num">${borradores}</div>
        <div class="s-label">Borradores</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-dest">★</div>
      <div class="summary-text">
        <div class="s-num">${destacados}</div>
        <div class="s-label">Destacados</div>
      </div>
    </div>
  </div>

  <div class="section-header">
    <span class="section-title">Detalle de Programas Académicos</span>
    <div class="section-line"></div>
    <span style="font-size:8px; color:#94a3b8; white-space:nowrap; margin-left:8px;">${total} registro(s)</span>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th class="center" style="width:40px;">ID</th>
          <th>Nombre del Programa / Curso</th>
          <th>Categoría</th>
          <th>Tipo</th>
          <th class="center">Inicio</th>
          <th class="center">Finalización</th>
          <th class="center">Estado Web</th>
          <th class="center">Dest.</th>
        </tr>
      </thead>
      <tbody>
        ${filas}
      </tbody>
    </table>
  </div>

  <div class="page-footer">
    <div class="footer-left">
      <strong>CENEFCO</strong> — Centro Nacional de Educación y Formación Continua<br>
      Documento generado automáticamente por el Sistema de Gestión Académica
    </div>
    <div class="footer-confidencial">
      Documento de uso interno — Confidencial
    </div>
    <div class="footer-right">
      Emitido: ${fecha} · ${hora}<br>
      Total de registros: ${total}
    </div>
  </div>

  <div class="btn-print-bar">
    <button class="btn-close" onclick="window.close()">Cerrar</button>
    <button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button>
  </div>

  <script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
  </script>
</body>
</html>`;

      const ventana = window.open('', '_blank', 'width=1200,height=820');
      if (!ventana) {
        this.toast.error('Bloqueado', 'Permite ventanas emergentes para generar el reporte PDF');
        return;
      }
      ventana.document.write(html);
      ventana.document.close();
    }).catch(() => {
      this.generandoPDF.set(false);
      this.toast.error('Error', 'No se pudo generar el reporte PDF');
    });
  }

  onSearch(event: Event): void {
    this.searchQuery.set((event.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void {
    this.pageIndex.set(page);
  }

  deleteCurso(id: number): void {
    Swal.fire({
      title: '¿Eliminar curso?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.cursoService.delete(id).subscribe({
          next: () => {
            this.toast.success('¡Eliminado!', 'El curso ha sido eliminado');
            this.refreshTrigger.update(n => n + 1);
          },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar el curso'))
        });
      }
    });
  }
}
