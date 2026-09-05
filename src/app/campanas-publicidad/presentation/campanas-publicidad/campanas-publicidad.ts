import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideMegaphone, lucideEye, lucideBarChart3, lucideSearch } from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaPublicidadService } from '../../application/services/campana-publicidad.service'
import { CampanaPublicidad } from '../../domain/models/campana-publicidad.model'

const PLATAFORMA_LABELS: Record<string, string> = {
  meta_ads: 'Meta Ads (Facebook/Instagram)',
  google_ads: 'Google Ads',
  tiktok_ads: 'TikTok Ads',
  otro: 'Otro',
}

const ESTADO_BADGE: Record<string, string> = {
  planificada: 'bg-default-200 text-default-600',
  activa: 'bg-success/10 text-success',
  pausada: 'bg-warning/10 text-warning',
  finalizada: 'bg-info/10 text-info',
  cancelada: 'bg-danger/10 text-danger',
}

@Component({
  selector: 'app-campanas-publicidad',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, NgIcon, PageTitle, Pagination, SearchableSelect],
  viewProviders: [provideIcons({ lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideMegaphone, lucideEye, lucideBarChart3, lucideSearch })],
  templateUrl: './campanas-publicidad.html',
})
export class CampanasPublicidad implements OnInit {
  private svc   = inject(CampanaPublicidadService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  campanas = signal<CampanaPublicidad[]>([])
  loading  = signal(true)
  deleting = signal<number | null>(null)

  currentPage = signal(1)
  pageSize    = 15
  total       = signal(0)
  query       = signal('')
  estadoFiltro     = signal<number | null>(null)
  plataformaFiltro = signal<number | null>(null)

  readonly estadoOptions: SelectOption[] = [
    { value: 'planificada', label: 'Planificada' },
    { value: 'activa', label: 'Activa' },
    { value: 'pausada', label: 'Pausada' },
    { value: 'finalizada', label: 'Finalizada' },
    { value: 'cancelada', label: 'Cancelada' },
  ]

  readonly plataformaOptions: SelectOption[] = [
    { value: 'meta_ads', label: 'Meta Ads (Facebook/Instagram)' },
    { value: 'google_ads', label: 'Google Ads' },
    { value: 'tiktok_ads', label: 'TikTok Ads' },
    { value: 'otro', label: 'Otro' },
  ]

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    this.svc.getAll({
      pageIndex: this.currentPage(),
      pageSize: this.pageSize,
      query: this.query(),
      estado: (this.estadoFiltro() as unknown as string) ?? undefined,
      plataforma: (this.plataformaFiltro() as unknown as string) ?? undefined,
    }).subscribe({
      next: (res) => {
        this.campanas.set(res.data)
        this.total.set(res.total)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de campañas.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onPageChange(page: number) {
    this.currentPage.set(page)
    this.load()
  }

  onBuscar(valor: string) {
    this.query.set(valor)
    this.currentPage.set(1)
    this.load()
  }

  onFiltrarEstado(estado: any) {
    this.estadoFiltro.set(estado)
    this.currentPage.set(1)
    this.load()
  }

  onFiltrarPlataforma(plataforma: any) {
    this.plataformaFiltro.set(plataforma)
    this.currentPage.set(1)
    this.load()
  }

  plataformaLabel(p: string): string {
    return PLATAFORMA_LABELS[p] ?? p
  }

  estadoBadgeClass(e: string): string {
    return ESTADO_BADGE[e] ?? 'bg-default-200 text-default-600'
  }

  eliminar(campana: CampanaPublicidad): void {
    Swal.fire({
      title: '¿Eliminar campaña?',
      text: `Se eliminará la campaña "${campana.nombre}". Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return
      this.deleting.set(campana.id)
      this.svc.delete(campana.id).subscribe({
        next: () => {
          this.toast.success('Eliminada', 'La campaña fue eliminada.')
          this.deleting.set(null)
          this.load()
        },
        error: (err) => {
          this.toast.error('Error', err?.error?.error ?? 'No se pudo eliminar la campaña.')
          this.deleting.set(null)
          this.cdr.detectChanges()
        }
      })
    })
  }
}
