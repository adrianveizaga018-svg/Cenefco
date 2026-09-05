import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideTrendingUp, lucideTrendingDown, lucideScale, lucideLoader, lucideAlertTriangle } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Apexchart } from '../../../common/components/apexchart/apexchart'
import { DashboardGastosService } from '../../application/services/dashboard-gastos.service'
import { DashboardGastos as DashboardGastosModel } from '../../domain/models/dashboard-gastos.model'
import { ApexOptions } from 'ng-apexcharts'

const NOMBRES_MES = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']

@Component({
  selector: 'app-dashboard-gastos',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, Apexchart, RouterLink],
  viewProviders: [provideIcons({ lucideTrendingUp, lucideTrendingDown, lucideScale, lucideLoader, lucideAlertTriangle })],
  templateUrl: './dashboard-gastos.html',
})
export class DashboardGastos implements OnInit {
  private svc = inject(DashboardGastosService)
  private cdr = inject(ChangeDetectorRef)

  loading = signal(true)
  data    = signal<DashboardGastosModel | null>(null)

  hoy  = new Date()
  anio = signal(this.hoy.getFullYear())
  mes  = signal(this.hoy.getMonth() + 1)

  categoriaChartOptions: (() => ApexOptions) | null = null

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    this.svc.getDashboard(this.anio(), this.mes()).subscribe({
      next: (res) => {
        this.data.set(res)
        this.buildCategoriaChart(res)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onCambiarPeriodo() {
    this.load()
  }

  nombreMes(mes: number): string {
    return NOMBRES_MES[mes] ?? String(mes)
  }

  yaConfirmadoEsteMes(fecha: string | null): boolean {
    if (!fecha) return false
    const f = new Date(fecha)
    return f.getFullYear() === this.anio() && (f.getMonth() + 1) === this.mes()
  }

  colorLinea(linea: string | null): string {
    switch (linea) {
      case 'diplomados':   return 'bg-indigo-500'
      case 'cursos_rm':    return 'bg-blue-500'
      case 'cursos_aval':  return 'bg-teal-500'
      default:             return 'bg-default-400'
    }
  }

  nombreLinea(linea: string | null): string {
    switch (linea) {
      case 'diplomados':  return 'Diplomados'
      case 'cursos_rm':   return 'Cursos con R.M.'
      case 'cursos_aval': return 'Cursos con aval'
      default:            return 'Sin línea asignada'
    }
  }

  porcentajeLinea(total: number): number {
    const totalGeneral = this.data()?.resumen_mes.total_gastos ?? 0
    return totalGeneral > 0 ? Math.round((total / totalGeneral) * 100) : 0
  }

  private buildCategoriaChart(data: DashboardGastosModel) {
    const categorias = data.gastos_por_categoria
    const labels = categorias.map(c => c.categoria_nombre ?? 'Sin categoría')
    const totales = categorias.map(c => c.total)

    this.categoriaChartOptions = () => ({
      series: [{ name: 'Gasto', data: totales }],
      chart: {
        type: 'bar',
        height: 300,
        toolbar: { show: false },
      },
      plotOptions: {
        bar: { horizontal: true, borderRadius: 4, barHeight: '60%' },
      },
      colors: ['#ef4444'],
      xaxis: {
        categories: labels,
        labels: { style: { fontSize: '11px', colors: '#94a3b8' } },
      },
      dataLabels: {
        enabled: true,
        formatter: (v: number) => 'Bs. ' + v,
        style: { fontSize: '11px' },
      },
      tooltip: {
        y: { formatter: (v: number) => 'Bs. ' + v },
      },
      grid: {
        borderColor: '#f1f5f9',
        strokeDashArray: 4,
      },
    })
  }
}
