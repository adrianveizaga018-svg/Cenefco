import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideLoader, lucideArrowLeft } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaPublicidadService } from '../../application/services/campana-publicidad.service'
import { ReporteCampana } from '../../domain/models/campana-publicidad.model'

@Component({
  selector: 'app-campanas-reporte',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, NgIcon, PageTitle],
  viewProviders: [provideIcons({ lucideLoader, lucideArrowLeft })],
  templateUrl: './campanas-reporte.html',
})
export class CampanasReporte implements OnInit {
  private svc   = inject(CampanaPublicidadService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  filas   = signal<ReporteCampana[]>([])
  loading = signal(true)

  fechaDesde = signal('')
  fechaHasta = signal('')

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    this.svc.getReporte(this.fechaDesde() || undefined, this.fechaHasta() || undefined).subscribe({
      next: (filas) => {
        this.filas.set([...filas].sort((a, b) => (b.retorno_aproximado ?? -1) - (a.retorno_aproximado ?? -1)))
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el reporte de campañas.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  filtrar() {
    this.load()
  }

  totalInvertido(): number {
    return this.filas().reduce((acc, f) => acc + f.total_invertido, 0)
  }

  totalAlcance(): number {
    return this.filas().reduce((acc, f) => acc + f.total_alcance, 0)
  }

  totalResultados(): number {
    return this.filas().reduce((acc, f) => acc + f.total_resultados, 0)
  }

  retornoGlobal(): number | null {
    const invertido = this.totalInvertido()
    const recaudado = this.filas().reduce((acc, f) => acc + (f.total_recaudado_curso ?? 0), 0)
    return invertido > 0 ? recaudado / invertido : null
  }
}
