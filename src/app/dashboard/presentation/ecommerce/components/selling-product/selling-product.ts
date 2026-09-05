import { Component, Input, OnChanges } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from "@ng-icons/core";
import { ApexOptions } from 'ng-apexcharts';
import { Apexchart } from "../../../../../common/components/apexchart/apexchart";
@Component({
  selector: 'app-selling-product',
  imports: [NgIcon, RouterLink, Apexchart],
  templateUrl: './selling-product.html',
  styles: ``
})
export class SellingProduct implements OnChanges {
  @Input() topTramites: any[] = [];

  chartOptions: (() => ApexOptions) | null = null;

  readonly colores = ['#2b7fff', '#14b8a6', '#8b5cf6', '#f59e0b', '#ef4444'];

  getColor(i: number): string {
    return this.colores[i % this.colores.length];
  }

  get maxSolicitudes(): number {
    return Math.max(...this.topTramites.map(t => t.total_solicitudes), 1);
  }

  getPct(val: number): number {
    return Math.round((val / this.maxSolicitudes) * 100);
  }

  ngOnChanges(): void {
    if (!this.topTramites.length) return;

    const nombres    = this.topTramites.map(t => t.nombre.length > 14 ? t.nombre.slice(0, 14) + '…' : t.nombre);
    const solicitudes = this.topTramites.map(t => t.total_solicitudes);

    this.chartOptions = () => ({
      series: solicitudes,
      labels: nombres,
      chart: {
        type: 'radialBar' as const,
        height: 260,
        toolbar: { show: false },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 900,
          animateGradually: { enabled: true, delay: 200 },
          dynamicAnimation: { enabled: true, speed: 400 },
        },
      },
      plotOptions: {
        radialBar: {
          offsetY: 0,
          startAngle: -135,
          endAngle: 135,
          hollow: { margin: 5, size: '30%' },
          dataLabels: {
            name: { fontSize: '11px', color: '#64748b' },
            value: {
              fontSize: '14px',
              fontWeight: 700,
              color: '#1e293b',
              formatter: (v: number) => Math.round(v) + '%',
            },
            total: {
              show: true,
              label: 'Top 1',
              fontSize: '11px',
              color: '#94a3b8',
              formatter: () => String(this.topTramites[0]?.total_solicitudes ?? 0) + ' sol.',
            },
          },
        },
      },
      colors: this.colores.slice(0, solicitudes.length),
      stroke: { lineCap: 'round' },
      legend: { show: false },
      tooltip: {
        y: { formatter: (v: number) => Math.round(v) + '% del máx.' },
      },
    });
  }
}
