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
  isSyncing = signal(false)
  isImporting = signal(false)

  currentPage = signal(1)
  pageSize    = 15
  total       = signal(0)
  query       = signal('')
  estadoFiltro     = signal<number | null>(null)
  plataformaFiltro = signal<number | null>(null)
  fechaDesdeFiltro = signal<string>('')
  fechaHastaFiltro = signal<string>('')
  cuentaFiltro = signal<string | null>(null)

  readonly cuentaOptions: SelectOption[] = [
    { value: 'act_1652384699141172', label: 'Cursos posgrados' },
    { value: 'act_1420958161864793', label: 'Master producciones' },
  ]

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
      fecha_desde: this.fechaDesdeFiltro() || undefined,
      fecha_hasta: this.fechaHastaFiltro() || undefined,
      cuenta_id: this.cuentaFiltro() || undefined,
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

  syncMeta() {
    this.isSyncing.set(true)
    this.svc.syncMeta({
      cuenta: this.cuentaFiltro() || undefined,
      fecha_desde: this.fechaDesdeFiltro() || undefined,
      fecha_hasta: this.fechaHastaFiltro() || undefined,
    }).subscribe({
      next: (res) => {
        this.toast.success('Sincronización Exitosa', res.message)
        this.isSyncing.set(false)
        this.load()
      },
      error: (err) => {
        this.toast.error('Error', 'Hubo un error al sincronizar con Meta Ads')
        this.isSyncing.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  importMeta() {
    this.isImporting.set(true)
    this.svc.importMeta().subscribe({
      next: (res) => {
        if (res.imported === 0) {
          this.toast.success('Meta Ads', 'Todas las campañas de Meta ya están importadas.')
        } else {
          this.toast.success('Importación Exitosa', `Se importaron ${res.imported} campañas de Meta Ads.`)
        }
        this.isImporting.set(false)
        this.load()
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron importar las campañas de Meta Ads.')
        this.isImporting.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  async exportarExcel() {
    this.toast.info('Exportando...', 'Generando Excel')
    this.svc.getAll({
      pageIndex: 1,
      pageSize: 10000,
      query: this.query(),
      estado: (this.estadoFiltro() as unknown as string) ?? undefined,
      plataforma: (this.plataformaFiltro() as unknown as string) ?? undefined,
      fecha_desde: this.fechaDesdeFiltro() || undefined,
      fecha_hasta: this.fechaHastaFiltro() || undefined,
    }).subscribe({
      next: async (res) => {
        const { utils, writeFile } = await import('xlsx')
        
        const data = res.data.map(c => {
          const leadsTotal = (c.leads ?? 0) + (c.leads_whatsapp ?? 0)
          return {
            'Campaña': c.nombre,
            'Curso / Propósito': c.programa_nombre || (c.proposito === 'curso' ? '—' : c.proposito),
            'Plataforma': c.plataforma,
            'Cuenta': c.cuenta_externa_id || '—',
            'Estado': c.estado,
            'En Testeo': c.en_testeo ? 'Sí' : 'No',
            'Encargado': c.responsable || '—',
            'Fecha Inicio': c.fecha_inicio,
            'Fecha Publicación': c.fecha_publicacion || '—',
            'Fecha Refuerzo': c.fecha_refuerzo || '—',
            'Fecha Fin': c.fecha_fin || '—',
            'Invertido (Bs)': c.presupuesto_bob || 0,
            'Alcance': c.metricas?.[0]?.alcance || 0,
            'Impresiones': c.metricas?.[0]?.impresiones || 0,
            'Clics': c.metricas?.[0]?.clics_enlace || 0,
            'Leads Meta': c.leads || 0,
            'Leads WhatsApp (Grupo)': c.leads_whatsapp || 0,
            'Total Leads': leadsTotal,
            'Costo por Lead (Bs)': c.costo_por_lead || 0,
            'Inscritos al Curso': c.inscritos_auto || 0,
            'Costo por Inscrito (Bs)': c.costo_por_inscrito || 0,
          }
        })

        const ws = utils.json_to_sheet(data)
        const wb = utils.book_new()
        utils.book_append_sheet(wb, ws, 'Campañas')
        
        writeFile(wb, `Reporte_Campanas_${new Date().getTime()}.xlsx`)
      }
    })
  }

  async exportarPDF() {
    this.toast.info('Exportando...', 'Generando PDF')
    this.svc.getAll({
      pageIndex: 1,
      pageSize: 10000,
      query: this.query(),
      estado: (this.estadoFiltro() as unknown as string) ?? undefined,
      plataforma: (this.plataformaFiltro() as unknown as string) ?? undefined,
      fecha_desde: this.fechaDesdeFiltro() || undefined,
      fecha_hasta: this.fechaHastaFiltro() || undefined,
    }).subscribe({
      next: async (res) => {
        const { jsPDF } = await import('jspdf')
        const autoTable = (await import('jspdf-autotable')).default

        // Utilidad para limpiar emojis y caracteres no soportados por jsPDF
        const cleanText = (text: string | null | undefined): string => {
          if (!text) return '—'
          return text.replace(/[\u{1F300}-\u{1FFFF}]/gu, '').replace(/[\u2600-\u26FF\u2700-\u27BF]/g, '').trim()
        }
        
        const doc = new jsPDF('landscape')
        
        doc.setFontSize(16)
        doc.text('Reporte de Campanas Publicitarias', 14, 15)
        doc.setFontSize(10)
        doc.text(`Fecha de generacion: ${new Date().toLocaleDateString()}`, 14, 22)
        if (this.cuentaFiltro()) {
          doc.text(`Portafolio: ${this.cuentaFiltro()}`, 14, 28)
        }

        const tableData = res.data.map(c => {
          const leadsTotal = (c.leads ?? 0) + (c.leads_whatsapp ?? 0)
          return [
            cleanText(c.nombre),
            cleanText(c.programa_nombre) || cleanText(c.proposito),
            c.plataforma,
            c.estado,
            c.fecha_inicio,
            cleanText(c.responsable),
            c.en_testeo ? 'Si' : 'No',
            `Bs ${(c.presupuesto_bob || 0).toFixed(2)}`,
            c.leads ?? 0,
            c.leads_whatsapp ?? 0,
            leadsTotal,
            c.costo_por_lead ? `Bs ${c.costo_por_lead.toFixed(2)}` : '—',
            c.inscritos_auto ?? '—',
            c.costo_por_inscrito ? `Bs ${c.costo_por_inscrito.toFixed(2)}` : '—',
          ]
        })

        autoTable(doc, {
          startY: this.cuentaFiltro() ? 34 : 28,
          head: [['Campana', 'Proposito', 'Plataforma', 'Estado', 'Inicio', 'Encargado', 'Testeo', 'Invertido', 'Leads Meta', 'Leads WA', 'Total Leads', 'Costo/Lead', 'Inscritos', 'Costo/Inscrito']],
          body: tableData,
          theme: 'grid',
          headStyles: { fillColor: [41, 128, 185], fontSize: 7 },
          bodyStyles: { fontSize: 7 },
          columnStyles: {
            0: { cellWidth: 40 },
          }
        })

        doc.save(`Reporte_Campanas_${new Date().getTime()}.pdf`)
      }
    })
  }
}
