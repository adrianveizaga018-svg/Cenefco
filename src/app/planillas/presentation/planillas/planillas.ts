import { Component, inject, signal, ChangeDetectorRef, OnInit, OnDestroy } from '@angular/core'
import { SlicePipe } from '@angular/common'
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideLoader, lucidePlus, lucideFileDown, lucideFileSpreadsheet, lucideEye, lucideX, lucideTrash2 } from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { PlanillaService } from '../../application/services/planilla.service'
import { ToastService } from '../../../common/application/services/toast.service'
import { Planilla } from '../../domain/models/planilla.model'

const NOMBRES_MES = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']

@Component({
  selector: 'app-planillas',
  standalone: true,
  imports: [RouterLink, NgIcon, PageTitle, Pagination, SlicePipe],
  viewProviders: [provideIcons({ lucideLoader, lucidePlus, lucideFileDown, lucideFileSpreadsheet, lucideEye, lucideX, lucideTrash2 })],
  templateUrl: './planillas.html',
})
export class Planillas implements OnInit, OnDestroy {
  private svc       = inject(PlanillaService)
  private toast     = inject(ToastService)
  private cdr       = inject(ChangeDetectorRef)
  private sanitizer = inject(DomSanitizer)

  planillas   = signal<Planilla[]>([])
  loading     = signal(true)
  currentPage = signal(1)
  pageSize    = 12
  total       = signal(0)
  descargando        = signal<number | null>(null)
  descargandoExcel   = signal<number | null>(null)
  eliminando         = signal<number | null>(null)

  previsualizando  = signal(false)
  cargandoPreview  = signal(false)
  previewPlanilla  = signal<Planilla | null>(null)
  previewUrl       = signal<SafeResourceUrl | null>(null)
  private previewBlobUrl: string | null = null

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    this.svc.getAll({ pageIndex: this.currentPage(), pageSize: this.pageSize }).subscribe({
      next: (res) => {
        this.planillas.set(res.data)
        this.total.set(res.total)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el historial de planillas.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onPageChange(page: number) {
    this.currentPage.set(page)
    this.load()
  }

  nombreMes(mes: number): string {
    return NOMBRES_MES[mes] ?? String(mes)
  }

  esDelMesActual(planilla: Planilla): boolean {
    const hoy = new Date()
    return planilla.anio === hoy.getFullYear() && planilla.mes === hoy.getMonth() + 1
  }

  eliminar(planilla: Planilla): void {
    Swal.fire({
      title: '¿Eliminar planilla?',
      text: `Se eliminará la planilla de ${this.nombreMes(planilla.mes)} ${planilla.anio} y el gasto de sueldos asociado (Bs. ${planilla.total}). Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.eliminando.set(planilla.id)
      this.svc.delete(planilla.id).subscribe({
        next: () => {
          this.toast.success('Eliminada', 'La planilla y su gasto asociado fueron eliminados.')
          this.eliminando.set(null)
          this.load()
        },
        error: (err) => {
          this.toast.error('Error', err?.error?.error ?? 'No se pudo eliminar la planilla.')
          this.eliminando.set(null)
          this.cdr.detectChanges()
        }
      })
    })
  }

  descargarPdf(planilla: Planilla): void {
    this.descargando.set(planilla.id)
    this.svc.exportPdf(planilla.id).subscribe({
      next: (blob) => {
        this.descargando.set(null)
        const url = URL.createObjectURL(blob)
        const a   = document.createElement('a')
        a.href     = url
        a.download = `planilla-${this.nombreMes(planilla.mes)}-${planilla.anio}.pdf`
        a.click()
        URL.revokeObjectURL(url)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el PDF de la planilla.')
        this.descargando.set(null)
        this.cdr.detectChanges()
      }
    })
  }

  descargarExcel(planilla: Planilla): void {
    this.descargandoExcel.set(planilla.id)
    this.svc.exportExcel(planilla.id).subscribe({
      next: (blob) => {
        this.descargandoExcel.set(null)
        const url = URL.createObjectURL(blob)
        const a   = document.createElement('a')
        a.href     = url
        a.download = `planilla-${this.nombreMes(planilla.mes)}-${planilla.anio}.xlsx`
        a.click()
        URL.revokeObjectURL(url)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el Excel de la planilla.')
        this.descargandoExcel.set(null)
        this.cdr.detectChanges()
      }
    })
  }

  previsualizar(planilla: Planilla): void {
    this.previewPlanilla.set(planilla)
    this.previsualizando.set(true)
    this.cargandoPreview.set(true)
    this.cdr.detectChanges()

    this.svc.exportPdf(planilla.id).subscribe({
      next: (blob) => {
        this.limpiarPreviewUrl()
        this.previewBlobUrl = URL.createObjectURL(blob)
        this.previewUrl.set(this.sanitizer.bypassSecurityTrustResourceUrl(this.previewBlobUrl))
        this.cargandoPreview.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar la vista previa de la planilla.')
        this.cargandoPreview.set(false)
        this.previsualizando.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  cerrarPreview(): void {
    this.previsualizando.set(false)
    this.previewPlanilla.set(null)
    this.limpiarPreviewUrl()
    this.cdr.detectChanges()
  }

  private limpiarPreviewUrl(): void {
    if (this.previewBlobUrl) {
      URL.revokeObjectURL(this.previewBlobUrl)
      this.previewBlobUrl = null
    }
    this.previewUrl.set(null)
  }

  ngOnDestroy(): void {
    this.limpiarPreviewUrl()
  }
}
