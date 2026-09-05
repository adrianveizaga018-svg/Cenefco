import { Component, ChangeDetectorRef, OnInit, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { ApexOptions } from 'ng-apexcharts';
import { Apexchart } from '../../../common/components/apexchart/apexchart';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { AnalyticsAdminService } from '../../application/services/analytics.service';
import { AnalyticsStats } from '../../domain/models/analytics.model';
import * as XLSX from 'xlsx';

@Component({
  selector: 'app-analytics',
  standalone: true,
  imports: [NgIcon, DecimalPipe, Apexchart, PageTitle],
  templateUrl: './analytics.html',
})
export class Analytics implements OnInit {
  private service = inject(AnalyticsAdminService);
  private cdr     = inject(ChangeDetectorRef);

  stats    = signal<AnalyticsStats | null>(null);
  loading  = signal(true);
  error    = signal(false);
  periodo  = signal<7 | 30 | 90>(30);
  periodos = [7, 30, 90] as (7 | 30 | 90)[];

  exportingPdf   = signal(false);
  exportingExcel = signal(false);

  porDiaChart:      (() => ApexOptions) | null = null;
  topPaginasChart:  (() => ApexOptions) | null = null;
  porHoraChart:     (() => ApexOptions) | null = null;
  dispositivoChart: (() => ApexOptions) | null = null;
  navegadorChart:   (() => ApexOptions) | null = null;
  soChart:          (() => ApexOptions) | null = null;

  ngOnInit(): void { this.load(); }

  setPeriodo(p: 7 | 30 | 90): void {
    if (this.periodo() === p) return;
    this.periodo.set(p);
    this.stats.set(null);
    this.error.set(false);
    this.loading.set(true);
    this.load();
  }

  formatDuracion(secs: number | null): string {
    if (!secs) return '—';
    if (secs < 60) return `${secs}s`;
    return `${Math.floor(secs / 60)}m ${secs % 60}s`;
  }

  async exportPdf(): Promise<void> {
    const s = this.stats();
    if (!s) return;
    this.exportingPdf.set(true);
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
      doc.text('CENEFCO — Analytics del Portal', 14, 12);
      doc.setFontSize(9);
      doc.setFont('helvetica', 'normal');
      doc.text(`Período: últimos ${this.periodo()} días   |   Generado: ${fecha}`, 14, 21);

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
          ['Visitas totales',  s.kpis.total_visitas.toLocaleString('es-BO')],
          ['Sesiones únicas',  s.kpis.sesiones_unicas.toLocaleString('es-BO')],
          ['Tiempo promedio',  this.formatDuracion(s.kpis.duracion_promedio)],
          ['Página más vista', s.kpis.pagina_top ?? '—'],
        ],
        headStyles:    { fillColor: [7, 67, 91], textColor: 255, fontSize: 9, fontStyle: 'bold' },
        bodyStyles:    { fontSize: 9, textColor: [50, 50, 50] },
        alternateRowStyles: { fillColor: [240, 247, 250] },
        margin:        { left: 14, right: 14 },
        theme:         'striped',
      });

      y = (doc as any).lastAutoTable.finalY + 10;

      if (s.por_dia.length) {
        if (y > 240) { doc.addPage(); addWatermark(); y = 20; }
        doc.setTextColor(7, 67, 91);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Visitas por Día', 14, y);
        y += 6;

        autoTable(doc, {
          startY: y,
          head:   [['Fecha', 'Visitas']],
          body:    s.por_dia.map(d => [d.fecha, d.visitas.toLocaleString('es-BO')]),
          headStyles: { fillColor: [99, 102, 241], textColor: 255, fontSize: 9, fontStyle: 'bold' },
          bodyStyles: { fontSize: 9 },
          alternateRowStyles: { fillColor: [246, 246, 254] },
          margin:  { left: 14, right: 14 },
          theme:   'striped',
        });
        y = (doc as any).lastAutoTable.finalY + 10;
      }

      if (s.top_paginas.length) {
        if (y > 220) { doc.addPage(); addWatermark(); y = 20; }
        doc.setTextColor(7, 67, 91);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Páginas Más Visitadas', 14, y);
        y += 6;

        autoTable(doc, {
          startY: y,
          head:   [['#', 'Ruta', 'Visitas']],
          body:    s.top_paginas.map((p, i) => [i + 1, p.ruta, p.visitas.toLocaleString('es-BO')]),
          headStyles: { fillColor: [139, 92, 246], textColor: 255, fontSize: 9, fontStyle: 'bold' },
          bodyStyles: { fontSize: 9 },
          columnStyles: { 0: { cellWidth: 12, halign: 'center' }, 2: { halign: 'right' } },
          alternateRowStyles: { fillColor: [249, 246, 255] },
          margin:  { left: 14, right: 14 },
          theme:   'striped',
        });
        y = (doc as any).lastAutoTable.finalY + 10;
      }

      if (s.por_pais.length) {
        if (y > 220) { doc.addPage(); addWatermark(); y = 20; }
        doc.setTextColor(7, 67, 91);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Top Países', 14, y);
        y += 6;

        autoTable(doc, {
          startY: y,
          head:   [['#', 'País', 'Visitas']],
          body:    s.por_pais.map((p, i) => [i + 1, p.pais, p.visitas.toLocaleString('es-BO')]),
          headStyles: { fillColor: [34, 197, 94], textColor: 255, fontSize: 9, fontStyle: 'bold' },
          bodyStyles: { fontSize: 9 },
          columnStyles: { 0: { cellWidth: 12, halign: 'center' }, 2: { halign: 'right' } },
          alternateRowStyles: { fillColor: [242, 253, 245] },
          margin:  { left: 14, right: 14 },
          theme:   'striped',
        });
        y = (doc as any).lastAutoTable.finalY + 10;
      }

      const triCols: [string, string, string, number[], string[]][] = [
        ['Dispositivos',     'Dispositivo', 'Visitas', [7,67,91],   s.por_dispositivo.map(d => d.dispositivo)],
        ['Navegadores',      'Navegador',   'Visitas', [59,130,246], s.por_navegador.map(n => n.navegador)],
        ['Sistemas Operativos', 'SO',       'Visitas', [249,115,22], s.por_so.map(o => o.so)],
      ];

      for (const [titulo, col1, col2, color, labels] of triCols) {
        const data = titulo === 'Dispositivos'      ? s.por_dispositivo.map((d, i) => [labels[i], d.visitas.toLocaleString('es-BO')])
                   : titulo === 'Navegadores'       ? s.por_navegador.map((n, i) => [labels[i], n.visitas.toLocaleString('es-BO')])
                   : s.por_so.map((o, i) => [labels[i], o.visitas.toLocaleString('es-BO')]);

        if (!data.length) continue;
        if (y > 230) { doc.addPage(); addWatermark(); y = 20; }

        doc.setTextColor(7, 67, 91);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text(titulo, 14, y);
        y += 6;

        autoTable(doc, {
          startY: y,
          head:   [[col1, col2]],
          body:    data,
          headStyles: { fillColor: color as [number, number, number], textColor: 255, fontSize: 9, fontStyle: 'bold' },
          bodyStyles: { fontSize: 9 },
          columnStyles: { 1: { halign: 'right' } },
          alternateRowStyles: { fillColor: [248, 249, 250] },
          margin:  { left: 14, right: 14 },
          theme:   'striped',
        });
        y = (doc as any).lastAutoTable.finalY + 10;
      }

      const totalPages = (doc as any).internal.getNumberOfPages();
      for (let i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(`CENEFCO — Página ${i} de ${totalPages}`, pw / 2, doc.internal.pageSize.getHeight() - 8, { align: 'center' });
      }

      doc.save(`analytics-cenefco-${this.periodo()}dias-${new Date().toISOString().slice(0, 10)}.pdf`);
    } finally {
      this.exportingPdf.set(false);
      this.cdr.detectChanges();
    }
  }

  exportExcel(): void {
    const s = this.stats();
    if (!s) return;
    this.exportingExcel.set(true);
    this.cdr.detectChanges();

    try {
      const wb = XLSX.utils.book_new();

      const kpisData = [
        ['Métrica', 'Valor'],
        ['Período (días)',   this.periodo()],
        ['Visitas totales',  s.kpis.total_visitas],
        ['Sesiones únicas',  s.kpis.sesiones_unicas],
        ['Tiempo promedio',  this.formatDuracion(s.kpis.duracion_promedio)],
        ['Página más vista', s.kpis.pagina_top ?? '—'],
      ];
      const wsKpis = XLSX.utils.aoa_to_sheet(kpisData);
      wsKpis['!cols'] = [{ wch: 22 }, { wch: 30 }];
      XLSX.utils.book_append_sheet(wb, wsKpis, 'Resumen');

      if (s.por_dia.length) {
        const wsDia = XLSX.utils.json_to_sheet(
          s.por_dia.map(d => ({ Fecha: d.fecha, Visitas: d.visitas }))
        );
        wsDia['!cols'] = [{ wch: 14 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsDia, 'Visitas por Día');
      }

      if (s.top_paginas.length) {
        const wsPag = XLSX.utils.json_to_sheet(
          s.top_paginas.map((p, i) => ({ '#': i + 1, Ruta: p.ruta, Visitas: p.visitas }))
        );
        wsPag['!cols'] = [{ wch: 5 }, { wch: 45 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsPag, 'Top Páginas');
      }

      if (s.por_hora.length) {
        const wsHora = XLSX.utils.json_to_sheet(
          Array.from({ length: 24 }, (_, h) => {
            const found = s.por_hora.find(x => x.hora === h);
            return { Hora: `${h}:00`, Visitas: found?.visitas ?? 0 };
          })
        );
        wsHora['!cols'] = [{ wch: 10 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsHora, 'Por Hora');
      }

      if (s.por_pais.length) {
        const wsPais = XLSX.utils.json_to_sheet(
          s.por_pais.map((p, i) => ({ '#': i + 1, País: p.pais, Visitas: p.visitas }))
        );
        wsPais['!cols'] = [{ wch: 5 }, { wch: 25 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsPais, 'Top Países');
      }

      if (s.por_dispositivo.length) {
        const wsDisp = XLSX.utils.json_to_sheet(
          s.por_dispositivo.map(d => ({ Dispositivo: d.dispositivo, Visitas: d.visitas }))
        );
        wsDisp['!cols'] = [{ wch: 18 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsDisp, 'Dispositivos');
      }

      if (s.por_navegador.length) {
        const wsNav = XLSX.utils.json_to_sheet(
          s.por_navegador.map(n => ({ Navegador: n.navegador, Visitas: n.visitas }))
        );
        wsNav['!cols'] = [{ wch: 18 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsNav, 'Navegadores');
      }

      if (s.por_so.length) {
        const wsSO = XLSX.utils.json_to_sheet(
          s.por_so.map(o => ({ 'Sistema Operativo': o.so, Visitas: o.visitas }))
        );
        wsSO['!cols'] = [{ wch: 22 }, { wch: 12 }];
        XLSX.utils.book_append_sheet(wb, wsSO, 'Sistemas Operativos');
      }

      XLSX.writeFile(wb, `analytics-cenefco-${this.periodo()}dias-${new Date().toISOString().slice(0, 10)}.xlsx`);
    } finally {
      this.exportingExcel.set(false);
      this.cdr.detectChanges();
    }
  }

  private load(): void {
    this.service.getStats(this.periodo()).subscribe({
      next: data => {
        this.stats.set(data);
        this.buildCharts(data);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.error.set(true);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  private buildCharts(data: AnalyticsStats): void {
    this.porDiaChart = () => ({
      chart:      { type: 'area' as const, height: 270, toolbar: { show: false }, animations: { enabled: false } },
      series:     [{ name: 'Visitas', data: data.por_dia.map(d => d.visitas) }],
      xaxis:      { categories: data.por_dia.map(d => d.fecha), labels: { rotate: -45, style: { fontSize: '10px' } }, tickAmount: 8 },
      yaxis:      { min: 0, labels: { style: { fontSize: '11px' } } },
      colors:     ['#6366f1'],
      stroke:     { curve: 'smooth' as const, width: 2 },
      fill:       { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
      dataLabels: { enabled: false },
      tooltip:    { x: { format: 'dd MMM yyyy' } },
      grid:       { borderColor: '#f1f5f9' },
    });

    this.topPaginasChart = () => ({
      chart:       { type: 'bar' as const, height: 320, toolbar: { show: false } },
      series:      [{ name: 'Visitas', data: data.top_paginas.map(p => p.visitas) }],
      plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4 } },
      xaxis:       { categories: data.top_paginas.map(p => p.ruta), labels: { style: { fontSize: '11px' } } },
      colors:      ['#8b5cf6'],
      dataLabels:  { enabled: true, style: { fontSize: '11px' } },
      grid:        { borderColor: '#f1f5f9' },
    });

    this.porHoraChart = () => {
      const horas = Array.from({ length: 24 }, (_, i) => i);
      const map   = new Map(data.por_hora.map(h => [h.hora, h.visitas]));
      return {
        chart:      { type: 'bar' as const, height: 200, toolbar: { show: false } },
        series:     [{ name: 'Visitas', data: horas.map(h => map.get(h) ?? 0) }],
        xaxis:      { categories: horas.map(h => `${h}h`), labels: { style: { fontSize: '10px' } } },
        colors:     ['#06b6d4'],
        dataLabels: { enabled: false },
        plotOptions: { bar: { borderRadius: 3, columnWidth: '70%' } },
        grid:       { borderColor: '#f1f5f9' },
      };
    };

    this.dispositivoChart = () => ({
      chart:       { type: 'donut' as const, height: 230 },
      series:      data.por_dispositivo.map(d => d.visitas),
      labels:      data.por_dispositivo.map(d => d.dispositivo),
      colors:      ['#6366f1', '#22c55e', '#f59e0b'],
      dataLabels:  { enabled: false },
      legend:      { position: 'bottom' as const, fontSize: '12px' },
      plotOptions: { pie: { donut: { size: '65%' } } },
    });

    this.navegadorChart = () => ({
      chart:       { type: 'donut' as const, height: 230 },
      series:      data.por_navegador.map(n => n.visitas),
      labels:      data.por_navegador.map(n => n.navegador),
      colors:      ['#3b82f6', '#f97316', '#ec4899', '#10b981', '#8b5cf6', '#ef4444', '#64748b', '#a855f7'],
      dataLabels:  { enabled: false },
      legend:      { position: 'bottom' as const, fontSize: '12px' },
      plotOptions: { pie: { donut: { size: '65%' } } },
    });

    this.soChart = () => ({
      chart:       { type: 'donut' as const, height: 230 },
      series:      data.por_so.map(s => s.visitas),
      labels:      data.por_so.map(s => s.so),
      colors:      ['#0ea5e9', '#a855f7', '#84cc16', '#f59e0b', '#64748b', '#ef4444'],
      dataLabels:  { enabled: false },
      legend:      { position: 'bottom' as const, fontSize: '12px' },
      plotOptions: { pie: { donut: { size: '65%' } } },
    });
  }
}
