import {
  Component, inject, signal, computed, ChangeDetectorRef, OnInit,
  ElementRef, HostListener,
} from '@angular/core';
import { DecimalPipe, NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ReporteCobrosService } from '../../application/services/reporte-cobros.service';
import {
  CursoOpcion, EstadoParticipante, ReporteCobrosResponse, ReporteParticipante,
} from '../../domain/models/reporte-cobros.model';
import { extractErrorMessage } from '../../../utils/http-error';
import * as XLSX from 'xlsx';

type S = { type: 'loading' } | { type: 'success'; data: ReporteCobrosResponse } | { type: 'error'; message: string } | { type: 'sin-curso' };

const ESTADO_LABELS: Record<EstadoParticipante, string> = {
  al_dia: 'Al día', en_mora: 'En mora', completo: 'Completo', sin_pagos: 'Sin pagos', sin_plan: 'Sin plan de pagos',
};
const ESTADO_CLASES: Record<EstadoParticipante, string> = {
  al_dia: 'bg-blue-100 text-blue-700',
  en_mora: 'bg-danger/10 text-danger',
  completo: 'bg-success/10 text-success',
  sin_pagos: 'bg-default-200 text-default-600',
  sin_plan: 'bg-amber-100 text-amber-700',
};
const CUOTA_LABELS: Record<string, string> = {
  pagada: 'Pagada', parcial: 'Parcial', vencida: 'Vencida', pendiente: 'Pendiente',
};
const CUOTA_CLASES: Record<string, string> = {
  pagada: 'bg-success/10 text-success',
  parcial: 'bg-amber-100 text-amber-700',
  vencida: 'bg-danger/10 text-danger',
  pendiente: 'bg-default-100 text-default-500',
};

@Component({
  selector: 'app-reporte-cobros',
  imports: [NgIcon, PageTitle, NgClass, FormsModule, DecimalPipe],
  templateUrl: './reporte-cobros.html',
})
export class ReporteCobros implements OnInit {
  private service = inject(ReporteCobrosService);
  private cdr     = inject(ChangeDetectorRef);
  private route   = inject(ActivatedRoute);
  private elRef   = inject(ElementRef);

  cursoFijo = signal(false);
  private presetIdImp: number | null = null;
  private queryParamsInitialized = false;

  cargandoCursos = signal(true);
  cursos     = signal<CursoOpcion[]>([]);
  cursoSel   = signal<CursoOpcion | null>(null);
  dropdownOpen  = signal(false);
  busquedaCurso = signal('');

  cursosFiltrados = computed(() => {
    const q = this.busquedaCurso().toLowerCase().trim();
    const todos = this.cursos();
    if (!q) return todos.slice(0, 50);
    return todos.filter(c =>
      c.nombre.toLowerCase().includes(q) ||
      c.periodo.toLowerCase().includes(q) ||
      c.gestion.toLowerCase().includes(q) ||
      c.docente.toLowerCase().includes(q)
    ).slice(0, 50);
  });

  fechaInicio = signal<string>('');
  fechaFin    = signal<string>('');
  conInactivos = signal(false);
  filtroEstado = signal<'todos' | EstadoParticipante>('todos');

  expandidos = signal<Record<number, boolean>>({});

  exportandoExcel = signal(false);
  exportandoPdf   = signal(false);

  private state = signal<S>({ type: 'sin-curso' });

  get isLoading() { return this.state().type === 'loading'; }
  get isError()   { return this.state().type === 'error'; }
  get errorMessage(): string { const s = this.state(); return s.type === 'error' ? s.message : ''; }
  get sinCurso()  { return this.state().type === 'sin-curso'; }
  get data(): ReporteCobrosResponse | null { const s = this.state(); return s.type === 'success' ? s.data : null; }

  participantesFiltrados = computed(() => {
    const d = this.data;
    if (!d) return [];
    const f = this.filtroEstado();
    if (f === 'todos') return d.participantes;
    return d.participantes.filter(p => p.estado_general === f);
  });

  @HostListener('document:click', ['$event'])
  onDocumentClick(e: MouseEvent): void {
    if (!this.elRef.nativeElement.contains(e.target)) {
      this.dropdownOpen.set(false);
    }
  }

  ngOnInit(): void {
    this.route.queryParamMap.pipe(takeUntilDestroyed()).subscribe(params => {
      const idImpParam = params.get('id_imp');
      const yaInicializado = this.queryParamsInitialized;
      this.queryParamsInitialized = true;

      if (idImpParam) {
        const idImp = Number(idImpParam);
        if (yaInicializado && idImp === this.presetIdImp && this.cursoFijo()) return;
        this.presetIdImp = idImp;
        this.cursoFijo.set(true);
        this.cargandoCursos.set(false);
        this.cargar();
      } else {
        if (yaInicializado && !this.cursoFijo() && this.presetIdImp === null) return;
        this.presetIdImp = null;
        this.cursoFijo.set(false);
        this.cursoSel.set(null);
        this.state.set({ type: 'sin-curso' });
        this.cargandoCursos.set(true);
        this.service.getCursos().subscribe({
          next: r => { this.cursos.set(r.data); this.cargandoCursos.set(false); this.cdr.detectChanges(); },
          error: () => { this.cargandoCursos.set(false); this.cdr.detectChanges(); },
        });
      }
    });
  }

  abrirDropdown(): void {
    this.busquedaCurso.set('');
    this.dropdownOpen.set(true);
    this.cdr.detectChanges();
    setTimeout(() => {
      const input = this.elRef.nativeElement.querySelector('input[type="text"]') as HTMLInputElement | null;
      input?.focus();
    }, 0);
  }

  seleccionarCurso(c: CursoOpcion | null): void {
    this.cursoSel.set(c);
    this.dropdownOpen.set(false);
    this.busquedaCurso.set('');
    this.cargar();
  }

  limpiarCurso(): void { this.seleccionarCurso(null); }

  setPresetFecha(preset: 'hoy' | 'mes' | 'anio' | 'mes-anterior' | 'todo'): void {
    const hoy = new Date();
    const iso = (d: Date) => d.toISOString().slice(0, 10);

    if (preset === 'todo') {
      this.fechaInicio.set('');
      this.fechaFin.set('');
    } else if (preset === 'hoy') {
      this.fechaInicio.set(iso(hoy));
      this.fechaFin.set(iso(hoy));
    } else if (preset === 'mes') {
      this.fechaInicio.set(iso(new Date(hoy.getFullYear(), hoy.getMonth(), 1)));
      this.fechaFin.set(iso(new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0)));
    } else if (preset === 'mes-anterior') {
      this.fechaInicio.set(iso(new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1)));
      this.fechaFin.set(iso(new Date(hoy.getFullYear(), hoy.getMonth(), 0)));
    } else if (preset === 'anio') {
      this.fechaInicio.set(iso(new Date(hoy.getFullYear(), 0, 1)));
      this.fechaFin.set(iso(new Date(hoy.getFullYear(), 11, 31)));
    }
    this.cargar();
  }

  cargar(): void {
    const idImp = this.presetIdImp ?? this.cursoSel()?.id_imp;
    if (!idImp) { this.state.set({ type: 'sin-curso' }); this.cdr.detectChanges(); return; }

    this.state.set({ type: 'loading' });
    this.cdr.detectChanges();

    this.service.getReporte({
      id_imp:        idImp,
      fecha_inicio:  this.fechaInicio() || undefined,
      fecha_fin:     this.fechaFin() || undefined,
      con_inactivos: this.conInactivos(),
    }).subscribe({
      next: data => { this.state.set({ type: 'success', data }); this.cdr.detectChanges(); },
      error: (err: HttpErrorResponse) => {
        this.state.set({ type: 'error', message: extractErrorMessage(err, 'No se pudo generar el reporte.') });
        this.cdr.detectChanges();
      },
    });
  }

  toggleParticipante(idIns: number): void {
    this.expandidos.update(cur => ({ ...cur, [idIns]: !cur[idIns] }));
  }

  isExpandido(idIns: number): boolean { return !!this.expandidos()[idIns]; }

  estadoLabel(e: EstadoParticipante): string { return ESTADO_LABELS[e] ?? e; }
  estadoClase(e: EstadoParticipante): string { return ESTADO_CLASES[e] ?? 'bg-default-100 text-default-500'; }
  cuotaLabel(e: string): string { return CUOTA_LABELS[e] ?? e; }
  cuotaClase(e: string): string { return CUOTA_CLASES[e] ?? 'bg-default-100 text-default-500'; }

  setFiltroEstado(f: 'todos' | EstadoParticipante): void { this.filtroEstado.set(f); }

  formatFecha(f: string | null): string {
    if (!f) return '—';
    try {
      return new Date(f + 'T00:00:00').toLocaleDateString('es-BO', { day: '2-digit', month: '2-digit', year: 'numeric' });
    } catch { return f; }
  }

  private nombreArchivo(ext: string): string {
    const curso = this.cursoSel()?.nombre ?? this.data?.participantes[0]?.curso_nombre ?? 'reporte';
    const slug = curso.toLowerCase().replace(/[^a-z0-9]+/g, '-').slice(0, 40);
    const fecha = new Date().toISOString().slice(0, 10);
    return `reporte-cobros-${slug}-${fecha}.${ext}`;
  }

  exportExcel(): void {
    const d = this.data;
    if (!d) return;
    this.exportandoExcel.set(true);
    this.cdr.detectChanges();

    try {
      const wb = XLSX.utils.book_new();

      const resumen: (string | number)[][] = [
        ['Métrica', 'Valor'],
        ['Curso', this.cursoSel()?.nombre ?? d.participantes[0]?.curso_nombre ?? '—'],
        ['Rango de fechas', this.fechaInicio() || this.fechaFin() ? `${this.fechaInicio() || '(inicio)'} a ${this.fechaFin() || '(fin)'}` : 'Todas'],
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
      wsResumen['!cols'] = [{ wch: 24 }, { wch: 36 }];
      XLSX.utils.book_append_sheet(wb, wsResumen, 'Resumen');

      const filasParticipantes = d.participantes.map(p => ({
        'Estudiante': p.estudiante_nombre,
        'CI': p.estudiante_ci ?? '',
        'Email': p.estudiante_email ?? '',
        'Celular': p.estudiante_celular ?? '',
        'Estado': this.estadoLabel(p.estado_general),
        'Cuotas pagadas': `${p.cuotas_pagadas}/${p.total_cuotas}`,
        'Cuotas vencidas': p.cuotas_vencidas,
        'Total plan (Bs.)': p.total_plan,
        'Total pagado (Bs.)': p.total_pagado,
        'Anticipos (Bs.)': p.total_anticipos,
        'Pendiente (Bs.)': p.pendiente ?? '',
      }));
      const wsParticipantes = XLSX.utils.json_to_sheet(filasParticipantes);
      wsParticipantes['!cols'] = [
        { wch: 28 }, { wch: 12 }, { wch: 26 }, { wch: 14 }, { wch: 16 },
        { wch: 14 }, { wch: 14 }, { wch: 14 }, { wch: 14 }, { wch: 12 }, { wch: 14 },
      ];
      XLSX.utils.book_append_sheet(wb, wsParticipantes, 'Participantes');

      const filasCuotas: Record<string, string | number>[] = [];
      for (const p of d.participantes) {
        for (const c of p.cuotas) {
          filasCuotas.push({
            'Estudiante': p.estudiante_nombre,
            'CI': p.estudiante_ci ?? '',
            'Cuota': c.cuota_nro ?? '',
            'Monto cuota (Bs.)': c.cuota_monto,
            'Fecha límite': c.cuota_fecha_fin ?? '',
            'Estado cuota': this.cuotaLabel(c.estado_cuota),
            'Monto pagado (Bs.)': c.monto_pagado,
            'N° pagos': c.nro_pagos,
            'Última fecha de pago': c.ultima_fecha_pago ?? '',
            'Verificación': c.estado_verificacion ?? '',
            'Días de atraso': c.dias_atraso,
          });
        }
      }
      const wsCuotas = XLSX.utils.json_to_sheet(filasCuotas);
      wsCuotas['!cols'] = [
        { wch: 28 }, { wch: 12 }, { wch: 12 }, { wch: 16 }, { wch: 14 },
        { wch: 14 }, { wch: 16 }, { wch: 10 }, { wch: 18 }, { wch: 14 }, { wch: 14 },
      ];
      XLSX.utils.book_append_sheet(wb, wsCuotas, 'Detalle por cuota');

      const filasPagos: Record<string, string | number>[] = [];
      for (const p of d.participantes) {
        for (const pago of p.pagos) {
          filasPagos.push({
            'Estudiante': p.estudiante_nombre,
            'CI': p.estudiante_ci ?? '',
            'Fecha de pago': pago.fecha_deposito ?? '',
            'Monto (Bs.)': pago.monto_pagado,
            'Método': pago.metodo_pago ?? '',
            'N° Boleta': pago.nro_boleta_bancaria ?? '',
            'Cuota': pago.cuota_nro ?? (pago.es_anticipo ? 'Anticipo' : ''),
            'Verificación': pago.estado_verificacion ?? '',
          });
        }
      }
      const wsPagos = XLSX.utils.json_to_sheet(filasPagos);
      wsPagos['!cols'] = [
        { wch: 28 }, { wch: 12 }, { wch: 14 }, { wch: 12 },
        { wch: 16 }, { wch: 16 }, { wch: 14 }, { wch: 14 },
      ];
      XLSX.utils.book_append_sheet(wb, wsPagos, 'Detalle de pagos');

      XLSX.writeFile(wb, this.nombreArchivo('xlsx'));
    } finally {
      this.exportandoExcel.set(false);
      this.cdr.detectChanges();
    }
  }

  async exportPdf(): Promise<void> {
    const d = this.data;
    if (!d) return;
    this.exportandoPdf.set(true);
    this.cdr.detectChanges();

    try {
      const { jsPDF } = await import('jspdf');
      const autoTable  = (await import('jspdf-autotable')).default;

      const doc   = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
      const pw    = doc.internal.pageSize.getWidth();
      const fecha = new Date().toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });
      const cursoNombre = this.cursoSel()?.nombre ?? d.participantes[0]?.curso_nombre ?? '—';

      doc.setFillColor(7, 67, 91);
      doc.rect(0, 0, pw, 22, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(14);
      doc.setFont('helvetica', 'bold');
      doc.text('CENEFCO — Reporte de Cobros', 10, 10);
      doc.setFontSize(9);
      doc.setFont('helvetica', 'normal');
      doc.text(`Curso: ${cursoNombre}`, 10, 17);
      doc.text(`Generado: ${fecha}`, pw - 10, 10, { align: 'right' });
      if (this.fechaInicio() || this.fechaFin()) {
        doc.text(`Rango: ${this.fechaInicio() || '(inicio)'} a ${this.fechaFin() || '(fin)'}`, pw - 10, 17, { align: 'right' });
      }

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

      const filas: (string | number)[][] = [];
      for (const p of d.participantes) {
        for (const [i, c] of p.cuotas.entries()) {
          filas.push([
            i === 0 ? p.estudiante_nombre : '',
            i === 0 ? (p.estudiante_ci ?? '—') : '',
            c.cuota_nro ?? '—',
            c.cuota_monto.toFixed(2),
            this.formatFecha(c.cuota_fecha_fin),
            this.cuotaLabel(c.estado_cuota),
            c.monto_pagado.toFixed(2),
            c.dias_atraso > 0 ? `${c.dias_atraso} d.` : '—',
          ]);
        }
        if (p.cuotas.length === 0) {
          filas.push([p.estudiante_nombre, p.estudiante_ci ?? '—', '—', '—', '—', this.estadoLabel(p.estado_general), p.total_pagado.toFixed(2), '—']);
        }
      }

      autoTable(doc, {
        startY: (doc as any).lastAutoTable.finalY + 6,
        head: [['Estudiante', 'CI', 'Cuota', 'Monto', 'Fecha límite', 'Estado', 'Pagado', 'Atraso']],
        body: filas,
        theme: 'striped',
        headStyles: { fillColor: [7, 67, 91] },
        styles: { fontSize: 7.5, cellPadding: 1.5 },
        columnStyles: { 3: { halign: 'right' }, 6: { halign: 'right' } },
        didDrawPage: () => {
          const ph = doc.internal.pageSize.getHeight();
          doc.setFontSize(7);
          doc.setTextColor(140);
          doc.text(`Página ${doc.getNumberOfPages()}`, pw - 10, ph - 6, { align: 'right' });
        },
      });

      doc.save(this.nombreArchivo('pdf'));
    } finally {
      this.exportandoPdf.set(false);
      this.cdr.detectChanges();
    }
  }
}
