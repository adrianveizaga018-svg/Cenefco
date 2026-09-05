import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { RouterLink } from '@angular/router'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideUsers, lucideEye, lucideSearch } from '@ng-icons/lucide'
import Swal from 'sweetalert2'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaLeadService } from '../../application/services/campana-lead.service'
import { CampanaLead } from '../../domain/models/campana-lead.model'

const ESTADO_BADGE: Record<string, string> = {
  activa: 'bg-success/10 text-success',
  cerrada: 'bg-default-200 text-default-600',
}

@Component({
  selector: 'app-campanas-leads',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, NgIcon, PageTitle, Pagination, SearchableSelect],
  viewProviders: [provideIcons({ lucidePencil, lucideTrash2, lucidePlus, lucideLoader, lucideUsers, lucideEye, lucideSearch })],
  templateUrl: './campanas-leads.html',
})
export class CampanasLeads implements OnInit {
  private svc   = inject(CampanaLeadService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  campanas = signal<CampanaLead[]>([])
  loading  = signal(true)
  deleting = signal<number | null>(null)

  currentPage  = signal(1)
  pageSize     = 15
  total        = signal(0)
  query        = signal('')
  estadoFiltro = signal<string | null>(null)

  readonly estadoOptions: SelectOption[] = [
    { value: 'activa', label: 'Activa' },
    { value: 'cerrada', label: 'Cerrada' },
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
      estado: this.estadoFiltro() ?? undefined,
    }).subscribe({
      next: (res) => {
        this.campanas.set(res.data)
        this.total.set(res.total)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista de campañas de leads.')
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

  estadoBadgeClass(e: string): string {
    return ESTADO_BADGE[e] ?? 'bg-default-200 text-default-600'
  }

  eliminar(campana: CampanaLead): void {
    Swal.fire({
      title: '¿Eliminar campaña de leads?',
      text: `Se eliminará la campaña "${campana.nombre}" junto con sus ${campana.total_leads} lead(s). Esta acción no se puede deshacer.`,
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
