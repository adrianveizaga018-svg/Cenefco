import {
  Component, inject, signal, computed, ChangeDetectorRef, OnInit, OnDestroy,
  DestroyRef, HostListener, ElementRef
} from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { HttpClient, HttpParams } from '@angular/common/http';
import { DecimalPipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil, switchMap } from 'rxjs/operators';
import { NgIcon } from '@ng-icons/core';
import { NgApexchartsModule } from 'ng-apexcharts';
import type { ApexOptions } from 'ng-apexcharts';
import { PageTitle } from '../../../common/components/page-title/page-title';
import * as XLSX from 'xlsx';

interface Datum { label: string; value: number; }

interface Dimension {
  key: string;
  titulo: string;
  subtitulo: string;
  tipo: 'donut' | 'bar' | 'bar-h';
  color: string;
  datos: Datum[];
}

interface ReporteData {
  curso: { id_imp: number; nombre: string; periodo: string; gestion: string; paralelo: string; docente: string; } | null;
  resumen: { total: number; activas: number; bajas: number; recaudado: number; };
  dimensiones: Dimension[];
}

interface Curso {
  id_imp: number;
  nombre: string;
  periodo: string;
  gestion: string;
  paralelo: string;
  docente: string;
  total_inscritos: number;
}

@Component({
  selector: 'app-inscripcion-reportes',
  imports: [NgIcon, NgApexchartsModule, PageTitle, DecimalPipe, RouterLink],
  templateUrl: './inscripcion-reportes.html',
})
export class InscripcionReportes implements OnInit, OnDestroy {
  private http       = inject(HttpClient);
  private cdr        = inject(ChangeDetectorRef);
  private destroyRef = inject(DestroyRef);
  private elRef      = inject(ElementRef);
  private route      = inject(ActivatedRoute);

  cursoFijo = signal(false);

  cargandoCursos = signal(true);
  cargando       = signal(false);
  error          = signal('');

  exportandoExcel = signal(false);
  exportandoPdf   = signal(false);

  cursos     = signal<Curso[]>([]);
  cursoSel   = signal<Curso | null>(null);
  data       = signal<ReporteData | null>(null);
  expandidos = signal<Record<string, boolean>>({});


  private chartsMap = new Map<string, ApexOptions>();


  private cargar$ = new Subject<HttpParams>();
  private destroy$ = new Subject<void>();


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

  @HostListener('document:click', ['$event'])
  onDocumentClick(e: MouseEvent): void {
    if (!this.elRef.nativeElement.contains(e.target)) {
      this.dropdownOpen.set(false);
    }
  }

  private presetIdImp: number | null = null;

  ngOnInit(): void {

    this.cargar$.pipe(
      switchMap(params =>
        this.http.get<ReporteData>('/api/v1/inscripciones/reportes', { params })
      ),
      takeUntil(this.destroy$),
    ).subscribe({
      next: d => {
        this.data.set(d);
        this.buildAllCharts(d.dimensiones);
        const exp: Record<string, boolean> = {};
        d.dimensiones.forEach((dim, i) => { exp[dim.key] = i < 4; });
        this.expandidos.set(exp);
        this.cargando.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.error.set('No se pudieron cargar los reportes.');
        this.cargando.set(false);
        this.cdr.detectChanges();
      },
    });

    const idImpParam = this.route.snapshot.queryParamMap.get('id_imp');
    if (idImpParam) {
      this.presetIdImp = Number(idImpParam);
      this.cursoFijo.set(true);
      this.cargandoCursos.set(false);
    } else {
      this.http.get<{ data: Curso[] }>('/api/v1/inscripciones/cursos')
        .pipe(takeUntilDestroyed(this.destroyRef))
        .subscribe({
          next: r => { this.cursos.set(r.data); this.cargandoCursos.set(false); this.cdr.detectChanges(); },
          error: () => { this.cargandoCursos.set(false); this.cdr.detectChanges(); },
        });
    }

    this.cargar();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
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

  seleccionarCurso(c: Curso | null): void {
    this.cursoSel.set(c);
    this.dropdownOpen.set(false);
    this.busquedaCurso.set('');
    this.cargar();
  }

  limpiarCurso(): void { this.seleccionarCurso(null); }

  cargar(): void {
    this.cargando.set(true);
    this.error.set('');
    let p = new HttpParams();
    const idImp = this.presetIdImp ?? this.cursoSel()?.id_imp;
    if (idImp) p = p.set('id_imp', idImp);
    this.cargar$.next(p);
  }

  toggle(key: string): void {
    const cur = { ...this.expandidos() };
    cur[key] = !cur[key];
    this.expandidos.set(cur);
    this.cdr.detectChanges();
  }

  isExpanded(key: string): boolean { return !!this.expandidos()[key]; }

  getChart(key: string): ApexOptions | null { return this.chartsMap.get(key) ?? null; }

  totalDim(datos: Datum[]): number { return datos.reduce((s, d) => s + d.value, 0); }

  pct(value: number, datos: Datum[]): string {
    const t = this.totalDim(datos);
    return t > 0 ? ((value / t) * 100).toFixed(1) : '0';
  }

  paleta(i: number): string {
    const p = ['#818cf8','#4ade80','#fbbf24','#f87171','#60a5fa','#a78bfa','#f472b6','#2dd4bf','#fb923c','#22d3ee'];
    return p[i % p.length];
  }

  paletaOscuro(i: number): string {
    const p = ['#4f46e5','#16a34a','#d97706','#dc2626','#2563eb','#7c3aed','#db2777','#0d9488','#ea580c','#0891b2'];
    return p[i % p.length];
  }

  private buildAllCharts(dims: Dimension[]): void {
    this.chartsMap.clear();
    for (const dim of dims) {
      if (dim.tipo === 'donut')     this.chartsMap.set(dim.key, this.buildDonut(dim));
      else if (dim.tipo === 'bar')  this.chartsMap.set(dim.key, this.buildBar(dim, false));
      else                          this.chartsMap.set(dim.key, this.buildBar(dim, true));
    }
  }

  private readonly PALETA = [
    '#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6',
    '#8b5cf6','#ec4899','#14b8a6','#f97316','#06b6d4',
  ];

  private readonly PALETA_GRADIENTES: { from: string; to: string }[] = [
    { from: '#818cf8', to: '#4f46e5' },
    { from: '#4ade80', to: '#16a34a' },
    { from: '#fbbf24', to: '#d97706' },
    { from: '#f87171', to: '#dc2626' },
    { from: '#60a5fa', to: '#2563eb' },
    { from: '#a78bfa', to: '#7c3aed' },
    { from: '#f472b6', to: '#db2777' },
    { from: '#2dd4bf', to: '#0d9488' },
    { from: '#fb923c', to: '#ea580c' },
    { from: '#22d3ee', to: '#0891b2' },
  ];

  exportCsv(dim: Dimension): void {
    const total = this.totalDim(dim.datos);
    const header = 'Categoría,Cantidad,Porcentaje\n';
    const rows = dim.datos
      .map(d => `"${d.label}",${d.value},${total > 0 ? ((d.value / total) * 100).toFixed(1) : '0'}%`)
      .join('\n');
    const blob = new Blob(['﻿' + header + rows], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `reporte-${dim.key}-${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  }

  private nombreArchivo(): string {
    const c = this.data()?.curso;
    return c ? `curso-${c.id_imp}` : 'global';
  }

  exportExcel(): void {
    const d = this.data();
    if (!d) return;
    this.exportandoExcel.set(true);
    this.cdr.detectChanges();

    try {
      const wb = XLSX.utils.book_new();

      const resumen: (string | number)[][] = [
        ['Métrica', 'Valor'],
        ['Curso', d.curso?.nombre ?? 'Todos los cursos'],
      ];
      if (d.curso) {
        resumen.push(
          ['Período', `${d.curso.periodo} — Gestión ${d.curso.gestion}`],
          ['Docente', d.curso.docente || '—'],
        );
      }
      resumen.push(
        ['Total inscripciones', d.resumen.total],
        ['Activas', d.resumen.activas],
        ['Bajas', d.resumen.bajas],
        ['Total recaudado (Bs.)', d.resumen.recaudado],
      );
      const wsResumen = XLSX.utils.aoa_to_sheet(resumen);
      wsResumen['!cols'] = [{ wch: 24 }, { wch: 36 }];
      XLSX.utils.book_append_sheet(wb, wsResumen, 'Resumen');

      const nombresUsados = new Set<string>();
      for (const dim of d.dimensiones) {
        if (!dim.datos.length) continue;
        const total = this.totalDim(dim.datos);
        const rows = dim.datos.map(item => ({
          [dim.titulo]: item.label,
          Cantidad: item.value,
          Porcentaje: total > 0 ? `${((item.value / total) * 100).toFixed(1)}%` : '0%',
        }));
        const ws = XLSX.utils.json_to_sheet(rows);
        ws['!cols'] = [{ wch: 32 }, { wch: 12 }, { wch: 12 }];
        let sheetName = dim.titulo.slice(0, 31).replace(/[[\]*/\\?:]/g, '');
        while (nombresUsados.has(sheetName)) sheetName = sheetName.slice(0, 28) + '_2';
        nombresUsados.add(sheetName);
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
      }

      XLSX.writeFile(wb, `reporte-inscripciones-${this.nombreArchivo()}-${new Date().toISOString().slice(0, 10)}.xlsx`);
    } finally {
      this.exportandoExcel.set(false);
      this.cdr.detectChanges();
    }
  }

  async exportPdf(): Promise<void> {
    const d = this.data();
    if (!d) return;
    this.exportandoPdf.set(true);
    this.cdr.detectChanges();

    try {
      const { jsPDF } = await import('jspdf');
      const autoTable  = (await import('jspdf-autotable')).default;

      const doc   = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
      const pw    = doc.internal.pageSize.getWidth();
      const ph    = doc.internal.pageSize.getHeight();
      const fecha = new Date().toLocaleDateString('es-BO', { day: '2-digit', month: 'long', year: 'numeric' });

      const logoBase64 = await fetch('assets/images/logo-sm.png')
        .then(r => r.blob())
        .then(b => new Promise<string>(res => {
          const reader = new FileReader();
          reader.onload = () => res(reader.result as string);
          reader.readAsDataURL(b);
        }));

      const addWatermark = () => {
        const wmSize = 90;
        const wmX    = (pw - wmSize) / 2;
        const wmY    = (ph - wmSize) / 2;
        doc.saveGraphicsState();
        (doc as any).setGState(new (doc as any).GState({ opacity: 0.06 }));
        doc.addImage(logoBase64, 'PNG', wmX, wmY, wmSize, wmSize);
        doc.restoreGraphicsState();
      };

      addWatermark();

      doc.setFillColor(7, 67, 91);
      doc.rect(0, 0, pw, 28, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(16);
      doc.setFont('helvetica', 'bold');
      doc.text('CENEFCO — Reporte de Inscripciones', 14, 12);
      doc.setFontSize(9);
      doc.setFont('helvetica', 'normal');
      const subtitulo = d.curso
        ? `${d.curso.nombre} — ${d.curso.periodo} Gestión ${d.curso.gestion}`
        : 'Todos los cursos';
      doc.text(`${subtitulo}   |   Generado: ${fecha}`, 14, 21);

      let y = 36;

      doc.setTextColor(7, 67, 91);
      doc.setFontSize(11);
      doc.setFont('helvetica', 'bold');
      doc.text('Resumen General', 14, y);
      y += 6;

      autoTable(doc, {
        startY: y,
        head:   [['Métrica', 'Valor']],
        body:   [
          ['Total inscripciones', d.resumen.total.toLocaleString('es-BO')],
          ['Activas',             d.resumen.activas.toLocaleString('es-BO')],
          ['Bajas',               d.resumen.bajas.toLocaleString('es-BO')],
          ['Total recaudado',     `Bs. ${d.resumen.recaudado.toLocaleString('es-BO', { minimumFractionDigits: 2 })}`],
        ],
        headStyles:    { fillColor: [7, 67, 91], textColor: 255, fontSize: 9, fontStyle: 'bold' },
        bodyStyles:    { fontSize: 9, textColor: [50, 50, 50] },
        alternateRowStyles: { fillColor: [240, 247, 250] },
        margin:        { left: 14, right: 14 },
        theme:         'striped',
      });

      y = (doc as any).lastAutoTable.finalY + 10;

      const coloresTabla: [number, number, number][] = [
        [99, 102, 241], [34, 197, 94], [245, 158, 11], [239, 68, 68],
        [59, 130, 246], [139, 92, 246], [236, 72, 153], [20, 184, 166],
      ];

      d.dimensiones.forEach((dim, i) => {
        if (!dim.datos.length) return;
        if (y > 240) { doc.addPage(); addWatermark(); y = 20; }

        const total = this.totalDim(dim.datos);
        doc.setTextColor(7, 67, 91);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text(dim.titulo, 14, y);
        y += 6;

        autoTable(doc, {
          startY: y,
          head:   [[dim.titulo, 'Cantidad', '%']],
          body:   dim.datos.map(item => [
            item.label,
            item.value.toLocaleString('es-BO'),
            total > 0 ? `${((item.value / total) * 100).toFixed(1)}%` : '0%',
          ]),
          headStyles: { fillColor: coloresTabla[i % coloresTabla.length], textColor: 255, fontSize: 9, fontStyle: 'bold' },
          bodyStyles: { fontSize: 9 },
          columnStyles: { 1: { halign: 'right' }, 2: { halign: 'right' } },
          alternateRowStyles: { fillColor: [248, 249, 250] },
          margin:  { left: 14, right: 14 },
          theme:   'striped',
        });
        y = (doc as any).lastAutoTable.finalY + 10;
      });

      const totalPages = (doc as any).internal.getNumberOfPages();
      for (let i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(`CENEFCO — Página ${i} de ${totalPages}`, pw / 2, doc.internal.pageSize.getHeight() - 8, { align: 'center' });
      }

      doc.save(`reporte-inscripciones-${this.nombreArchivo()}-${new Date().toISOString().slice(0, 10)}.pdf`);
    } finally {
      this.exportandoPdf.set(false);
      this.cdr.detectChanges();
    }
  }

  private toolbar(filename: string): NonNullable<ApexOptions['chart']>['toolbar'] {
    return {
      show: true,
      tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false },
      export: {
        csv: { filename, columnDelimiter: ',', headerCategory: 'Categoría', headerValue: 'Cantidad' },
        svg: { filename },
        png: { filename },
      },
    };
  }

  private buildDonut(dim: Dimension): ApexOptions {
    const filename = `reporte-${dim.key}-${new Date().toISOString().slice(0, 10)}`;
    const total = dim.datos.reduce((s, d) => s + d.value, 0);
    return {
      chart: {
        type: 'donut', height: 340,
        animations: {
          enabled: true, speed: 600,
          animateGradually: { enabled: true, delay: 80 },
          dynamicAnimation: { enabled: true, speed: 350 },
        },
        dropShadow: { enabled: true, top: 4, left: 0, blur: 10, color: '#000', opacity: 0.08 },
        toolbar: this.toolbar(filename),
      },
      series: dim.datos.map(d => d.value),
      labels: dim.datos.map(d => d.label),
      colors: this.PALETA,
      legend: {
        position: 'bottom', fontSize: '13px', fontFamily: 'inherit',
        itemMargin: { horizontal: 12, vertical: 4 },
        markers: { size: 8 } as any,
        formatter: (label: string, opts: any) => {
          const pct = total > 0 ? ((opts.w.globals.series[opts.seriesIndex] / total) * 100).toFixed(1) : '0';
          return `${label} <b>${pct}%</b>`;
        },
      },
      plotOptions: {
        pie: {
          expandOnClick: true,
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: { fontSize: '13px', fontFamily: 'inherit', color: '#6b7280', offsetY: 4 },
              value: {
                fontSize: '26px', fontWeight: '800', fontFamily: 'inherit', color: '#111827', offsetY: -4,
                formatter: (v: string) => Number(v).toLocaleString(),
              },
              total: {
                show: true, label: 'Total', fontSize: '13px', fontFamily: 'inherit', color: '#9ca3af',
                formatter: (w: any) => w.globals.seriesTotals.reduce((a: number, b: number) => a + b, 0).toLocaleString(),
              },
            },
          },
        },
      },
      dataLabels: {
        enabled: true,
        formatter: (_v: number, opts: any) => {
          const pct = total > 0 ? ((opts.w.globals.series[opts.seriesIndex] / total) * 100).toFixed(1) : '0';
          return `${pct}%`;
        },
        style: { fontSize: '11px', fontWeight: '700', fontFamily: 'inherit' },
        dropShadow: { enabled: false },
      },
      stroke: { width: 3, colors: ['#ffffff'] },
      tooltip: {
        style: { fontSize: '13px', fontFamily: 'inherit' },
        y: {
          formatter: (v: number) => {
            const pct = total > 0 ? ` (${((v / total) * 100).toFixed(1)}%)` : '';
            return `${v.toLocaleString()} inscripciones${pct}`;
          },
        },
      },
      states: {
        hover: { filter: { type: 'darken', value: 0.88 } as any },
        active: { filter: { type: 'darken', value: 0.75 } as any },
      },
      responsive: [{ breakpoint: 480, options: { chart: { height: 280 }, legend: { position: 'bottom' } } }],
    };
  }

  private buildBar(dim: Dimension, horizontal: boolean): ApexOptions {
    const height = horizontal ? Math.max(260, dim.datos.length * 44 + 60) : 300;
    const filename = `reporte-${dim.key}-${new Date().toISOString().slice(0, 10)}`;
    const n = dim.datos.length;

    const colores = dim.datos.map((_, i) => this.PALETA_GRADIENTES[i % this.PALETA_GRADIENTES.length].from);
    const total = dim.datos.reduce((s, d) => s + d.value, 0);
    return {
      chart: {
        type: 'bar', height,
        animations: {
          enabled: true, speed: 700,
          animateGradually: { enabled: true, delay: 60 },
          dynamicAnimation: { enabled: true, speed: 400 },
        },
        dropShadow: { enabled: true, top: 3, left: 0, blur: 8, color: '#000', opacity: 0.06 },
        toolbar: this.toolbar(filename),
      },
      series: [{ name: 'Inscripciones', data: dim.datos.map(d => d.value) }],
      xaxis: {
        categories: dim.datos.map(d => d.label),
        labels: {
          style: { fontSize: '12px', fontFamily: 'inherit', colors: Array(n).fill('#6b7280') },
          trim: true, maxHeight: 80,
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      yaxis: {
        labels: { style: { fontSize: '11px', fontFamily: 'inherit', colors: '#9ca3af' } },
        ...(horizontal ? {} : { tickAmount: 4 }),
      },
      colors: colores,
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: horizontal ? 'horizontal' : 'vertical',
          shadeIntensity: 0.3,
          gradientToColors: dim.datos.map((_, i) => this.PALETA_GRADIENTES[i % this.PALETA_GRADIENTES.length].to),
          inverseColors: false,
          opacityFrom: 1,
          opacityTo: 0.85,
          stops: [0, 100],
        },
      },
      plotOptions: {
        bar: {
          horizontal,
          borderRadius: 6,
          borderRadiusApplication: 'end',
          columnWidth: n <= 5 ? '40%' : '60%',
          barHeight: '60%',
          distributed: true,
          dataLabels: { position: horizontal ? 'center' : 'top' },
        },
      },
      dataLabels: {
        enabled: true,
        formatter: (v: number) => v > 0 ? v.toLocaleString() : '',
        style: {
          fontSize: '11px', fontWeight: '700', fontFamily: 'inherit',
          colors: horizontal ? ['#fff'] : colores,
        },
        offsetY: horizontal ? 0 : -6,
        dropShadow: { enabled: horizontal, top: 1, left: 0, blur: 2, color: '#000', opacity: 0.25 },
      },
      legend: { show: false },
      grid: {
        borderColor: '#f3f4f6',
        strokeDashArray: 4,
        xaxis: { lines: { show: horizontal } },
        yaxis: { lines: { show: !horizontal } },
        padding: { top: 4, right: 8, bottom: 0, left: 8 },
      },
      tooltip: {
        style: { fontSize: '13px', fontFamily: 'inherit' },
        y: {
          formatter: (v: number) => {
            const pct = total > 0 ? ` (${((v / total) * 100).toFixed(1)}%)` : '';
            return `${v.toLocaleString()} inscripciones${pct}`;
          },
        },
      },
      states: {
        hover: { filter: { type: 'darken', value: 0.85 } as any },
        active: { filter: { type: 'darken', value: 0.7 } as any },
      },
      responsive: [{ breakpoint: 480, options: { chart: { height: horizontal ? 240 : 220 } } }],
    };
  }
}
