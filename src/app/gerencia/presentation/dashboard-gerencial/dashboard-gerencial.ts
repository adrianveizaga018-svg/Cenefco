import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { DashboardGerencialService, DashboardData } from '../../application/services/dashboard-gerencial.service';

type Periodo = 'hoy' | 'semana' | 'mes' | 'trimestre' | 'anio' | 'personalizado';

@Component({
  selector: 'app-dashboard-gerencial',
  standalone: true,
  imports: [CommonModule, FormsModule, NgIcon],
  templateUrl: './dashboard-gerencial.html',
})
export default class DashboardGerencialComponent implements OnInit {
  private svc = inject(DashboardGerencialService);

  isLoading = signal(true);
  data = signal<DashboardData | null>(null);
  periodoActivo = signal<Periodo>('mes');

  fechaDesde = signal('');
  fechaHasta = signal('');

  periodosBtns: {key: Periodo, label: string}[] = [
    {key:'hoy', label:'Hoy'},
    {key:'semana', label:'Semana'},
    {key:'mes', label:'Este Mes'},
    {key:'trimestre', label:'Trimestre'},
    {key:'anio', label:'Año'}
  ];

  ngOnInit() {
    this.aplicarPeriodo('mes');
  }

  aplicarPeriodo(p: Periodo) {
    this.periodoActivo.set(p);
    const hoy = new Date();
    let desde: Date, hasta: Date;

    switch (p) {
      case 'hoy':
        desde = hasta = new Date(hoy);
        break;
      case 'semana':
        desde = new Date(hoy);
        desde.setDate(hoy.getDate() - hoy.getDay());
        hasta = new Date(hoy);
        break;
      case 'mes':
        desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        hasta = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        break;
      case 'trimestre':
        const trimestre = Math.floor(hoy.getMonth() / 3);
        desde = new Date(hoy.getFullYear(), trimestre * 3, 1);
        hasta = new Date(hoy.getFullYear(), trimestre * 3 + 3, 0);
        break;
      case 'anio':
        desde = new Date(hoy.getFullYear(), 0, 1);
        hasta = new Date(hoy.getFullYear(), 11, 31);
        break;
      default:
        this.cargarDatos(this.fechaDesde(), this.fechaHasta());
        return;
    }
    const fmt = (d: Date) => {
       const tzoffset = (new Date()).getTimezoneOffset() * 60000;
       return (new Date(d.getTime() - tzoffset)).toISOString().split('T')[0];
    };
    this.fechaDesde.set(fmt(desde));
    this.fechaHasta.set(fmt(hasta));
    this.cargarDatos(fmt(desde), fmt(hasta));
  }

  cargarPersonalizado() {
    if (this.fechaDesde() && this.fechaHasta()) {
      this.periodoActivo.set('personalizado');
      this.cargarDatos(this.fechaDesde(), this.fechaHasta());
    }
  }

  private cargarDatos(desde: string, hasta: string) {
    this.isLoading.set(true);
    this.svc.getDashboard(desde, hasta).subscribe({
      next: (res) => { this.data.set(res); this.isLoading.set(false); },
      error: () => this.isLoading.set(false),
    });
  }

  formatMes(mes: string): string {
    if (!mes) return '';
    const [y, m] = mes.split('-');
    const nombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return `${nombres[parseInt(m, 10) - 1]} ${y}`;
  }

  get maxIngreso(): number {
    const items = this.data()?.graficos?.ingresos_por_mes ?? [];
    return Math.max(...items.map(i => i.total ?? 0), 1);
  }

  estadoLabel(estado: string): string {
    const map: Record<string, string> = {
      nuevo: 'Nuevos', contactado: 'Contactados', interesado: 'Interesados',
      inscrito: 'Inscritos', no_interesado: 'No Interesados',
    };
    return map[estado] ?? estado;
  }

  estadoColor(estado: string): string {
    const map: Record<string, string> = {
      nuevo: 'bg-red-500', contactado: 'bg-yellow-400', interesado: 'bg-blue-400',
      inscrito: 'bg-green-500', no_interesado: 'bg-gray-400',
    };
    return map[estado] ?? 'bg-slate-400';
  }

  get totalLeadsGrafico(): number {
    return (this.data()?.graficos?.leads_por_estado ?? []).reduce((s, i) => s + (i.total ?? 0), 0);
  }
}