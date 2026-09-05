import { Component, Input, OnChanges } from '@angular/core';
import { ApexOptions } from 'ng-apexcharts';
import { Apexchart } from "../../../../../common/components/apexchart/apexchart";
@Component({
  selector: 'app-sales-revenue-chart',
  imports: [Apexchart],
  templateUrl: './sales-revenue-chart.html',
  styles: ``
})
export class SalesRevenueChart implements OnChanges {
  @Input() actividadMensual: any[] = [];

  totalTramites  = 0;
  totalPublicaciones = 0;
  chartOptions: (() => ApexOptions) | null = null;

  ngOnChanges(): void {
    const meses        = this.actividadMensual.map(a => a.mes);
    const tramites     = this.actividadMensual.map(a => a.tramites);
    const comunicados  = this.actividadMensual.map(a => a.comunicados);
    const noticias     = this.actividadMensual.map(a => a.noticias);

    this.totalTramites       = tramites.reduce((s, v) => s + v, 0);
    this.totalPublicaciones  = comunicados.reduce((s, v) => s + v, 0) + noticias.reduce((s, v) => s + v, 0);

    this.chartOptions = () => ({
      series: [
        { name: 'Trámites',    data: tramites,    type: 'area' },
        { name: 'Comunicados', data: comunicados, type: 'line' },
        { name: 'Noticias',    data: noticias,    type: 'line' },
      ],
      chart: {
        type: 'area',
        height: 300,
        toolbar: { show: false },
        zoom: { enabled: false },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 900,
          animateGradually: { enabled: true, delay: 120 },
          dynamicAnimation: { enabled: true, speed: 400 },
        },
      },
      stroke: {
        curve: 'smooth',
        width: [3, 2, 2],
        dashArray: [0, 5, 8],
      },
      fill: {
        type: ['gradient', 'solid', 'solid'],
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.45,
          opacityTo: 0.03,
          stops: [0, 85, 100],
          colorStops: [[
            { offset: 0,   color: '#2b7fff', opacity: 0.45 },
            { offset: 100, color: '#2b7fff', opacity: 0.03 },
          ]],
        },
        opacity: [1, 0, 0],
      },
      colors: ['#2b7fff', '#f59e0b', '#22c55e'],
      xaxis: {
        categories: meses,
        labels: { style: { fontSize: '12px', colors: '#94a3b8' } },
        axisBorder: { show: false },
        axisTicks:  { show: false },
      },
      yaxis: {
        labels: {
          style: { fontSize: '11px', colors: ['#94a3b8'] },
          formatter: (v: number) => String(Math.round(v)),
        },
      },
      tooltip: {
        shared: true,
        intersect: false,
        y: [
          { formatter: (v: number) => v + ' trámites' },
          { formatter: (v: number) => v + ' comunicados' },
          { formatter: (v: number) => v + ' noticias' },
        ],
      },
      grid: {
        borderColor: '#f1f5f9',
        strokeDashArray: 4,
        padding: { top: -10, right: 10, bottom: 0, left: 10 },
      },
      legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontSize: '12px',
      },
      markers: {
        size: [4, 4, 4],
        strokeWidth: 2,
        hover: { size: 7 },
      },
      dataLabels: { enabled: false },
    });
  }
}
