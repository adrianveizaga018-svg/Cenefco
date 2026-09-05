import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideCheck, lucideX, lucideDownload, lucideLoader } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { Pagination } from '../../../common/components/pagination/pagination'
import { ToastService } from '../../../common/application/services/toast.service'
import { CertConfigProgramaService } from '../../application/services/cert-config-programa.service'
import { CertSolicitud } from '../../domain/models/cert-config-programa.model'

const ESTADOS = [
  { value: '', label: 'Todos' },
  { value: 'pendiente_revision', label: 'Pendiente revisión' },
  { value: 'pendiente_pago', label: 'Pendiente pago' },
  { value: 'generado', label: 'Generado' },
  { value: 'rechazado', label: 'Rechazado' },
]

@Component({
  selector: 'app-cert-solicitudes',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, Pagination],
  viewProviders: [provideIcons({ lucideCheck, lucideX, lucideDownload, lucideLoader })],
  templateUrl: './cert-solicitudes.html',
})
export class CertSolicitudes implements OnInit {
  private svc   = inject(CertConfigProgramaService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  solicitudes = signal<CertSolicitud[]>([])
  total       = signal(0)
  loading     = signal(true)
  pageIndex   = signal(1)
  pageSize    = 15
  estadoFiltro = signal('')

  estados = ESTADOS
  rechazandoId = signal<number | null>(null)
  notaRechazo  = signal('')

  ngOnInit() {
    this.load()
  }

  load() {
    this.loading.set(true)
    const params: any = { pageIndex: this.pageIndex(), pageSize: this.pageSize }
    if (this.estadoFiltro()) params.estado = this.estadoFiltro()
    this.svc.getSolicitudes(params).subscribe({
      next: (res) => {
        this.solicitudes.set(res.data)
        this.total.set(res.total)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la lista.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  onPageChange(page: number) {
    this.pageIndex.set(page)
    this.load()
  }

  onEstadoChange(val: string) {
    this.estadoFiltro.set(val)
    this.pageIndex.set(1)
    this.load()
  }

  aprobar(sol: CertSolicitud) {
    this.svc.aprobar(sol.id).subscribe({
      next: (updated) => {
        this.solicitudes.update(list => list.map(s => s.id === updated.id ? updated : s))
        this.toast.success('Aprobado', 'Certificado generado correctamente.')
        this.cdr.detectChanges()
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo aprobar.')
        this.cdr.detectChanges()
      }
    })
  }

  iniciarRechazo(id: number) {
    this.rechazandoId.set(id)
    this.notaRechazo.set('')
    this.cdr.detectChanges()
  }

  confirmarRechazo() {
    const id = this.rechazandoId()
    if (!id || !this.notaRechazo()) return
    this.svc.rechazar(id, this.notaRechazo()).subscribe({
      next: (updated) => {
        this.solicitudes.update(list => list.map(s => s.id === updated.id ? updated : s))
        this.rechazandoId.set(null)
        this.toast.success('Rechazado', 'Solicitud rechazada.')
        this.cdr.detectChanges()
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo rechazar.')
        this.cdr.detectChanges()
      }
    })
  }

  estadoBadgeClass(estado: string): string {
    const map: Record<string, string> = {
      generado:            'bg-success/10 text-success',
      pendiente_revision:  'bg-warning/10 text-warning',
      pendiente_pago:      'bg-info/10 text-info',
      rechazado:           'bg-danger/10 text-danger',
      no_participa:        'bg-default-200 text-default-500',
    }
    return map[estado] ?? 'bg-default-200 text-default-500'
  }
}
