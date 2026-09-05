import { Component, ChangeDetectorRef, inject, signal, OnInit, OnDestroy } from '@angular/core'
import { CommonModule } from '@angular/common'
import { FormsModule } from '@angular/forms'
import { ActivatedRoute, RouterLink } from '@angular/router'
import { HttpErrorResponse } from '@angular/common/http'
import { NgIcon, provideIcons } from '@ng-icons/core'
import {
  lucideLoader, lucideArrowLeft, lucidePhone, lucideMessageSquare,
  lucideMail, lucideUsers, lucideCalendar, lucideClipboardList,
  lucideChevronDown, lucideCheck, lucidePlus, lucideX, lucideUser,
} from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { ToastService } from '../../../common/application/services/toast.service'
import { LeadService } from '../../application/services/lead.service'
import { LeadSeguimientoService } from '../../application/services/lead-seguimiento.service'
import {
  Lead, LeadSeguimiento, EstadoLead,
  TipoSeguimiento, ResultadoSeguimiento, CreateSeguimientoPayload,
} from '../../domain/models/campana-lead.model'

const ESTADO_LABEL: Record<EstadoLead, string> = {
  nuevo:       'Nuevo',
  contactado:  'Contactado',
  interesado:  'Interesado',
  inscrito:    'Inscrito',
  descartado:  'Descartado',
}

const ESTADO_CLASS: Record<EstadoLead, string> = {
  nuevo:       'bg-blue-100 text-blue-700',
  contactado:  'bg-yellow-100 text-yellow-700',
  interesado:  'bg-orange-100 text-orange-700',
  inscrito:    'bg-success/10 text-success',
  descartado:  'bg-default-200 text-default-500',
}

const TIPO_ICON: Record<TipoSeguimiento, string> = {
  llamada:  'lucidePhone',
  whatsapp: 'lucideMessageSquare',
  correo:   'lucideMail',
  reunion:  'lucideUsers',
  otro:     'lucideClipboardList',
}

const RESULTADO_LABEL: Record<ResultadoSeguimiento, string> = {
  no_contesto:    'No contestó',
  contactado:     'Contactado',
  interesado:     'Interesado',
  no_interesado:  'No interesado',
  inscrito:       'Inscrito',
}

const RESULTADO_CLASS: Record<ResultadoSeguimiento, string> = {
  no_contesto:    'bg-default-200 text-default-600',
  contactado:     'bg-blue-100 text-blue-700',
  interesado:     'bg-orange-100 text-orange-700',
  no_interesado:  'bg-danger/10 text-danger',
  inscrito:       'bg-success/10 text-success',
}

@Component({
  selector: 'app-lead-detail',
  standalone: true,
  imports: [CommonModule, FormsModule, NgIcon, RouterLink, PageTitle],
  viewProviders: [provideIcons({
    lucideLoader, lucideArrowLeft, lucidePhone, lucideMessageSquare,
    lucideMail, lucideUsers, lucideCalendar, lucideClipboardList,
    lucideChevronDown, lucideCheck, lucidePlus, lucideX, lucideUser,
  })],
  templateUrl: './lead-detail.html',
})
export class LeadDetail implements OnInit {
  private route        = inject(ActivatedRoute)
  private leadSvc      = inject(LeadService)
  private seguimSvc    = inject(LeadSeguimientoService)
  private toast        = inject(ToastService)
  private cdr          = inject(ChangeDetectorRef)

  campanaLeadId = 0
  leadId        = 0

  lead         = signal<Lead | null>(null)
  seguimientos = signal<LeadSeguimiento[]>([])
  isLoading    = signal(true)
  hasError     = signal(false)

  readonly estadoLabel = ESTADO_LABEL
  readonly estadoClass = ESTADO_CLASS
  readonly tipoIcon    = TIPO_ICON
  readonly resultadoLabel = RESULTADO_LABEL
  readonly resultadoClass = RESULTADO_CLASS

  readonly estados: EstadoLead[] = ['nuevo','contactado','interesado','inscrito','descartado']
  readonly tipos: TipoSeguimiento[] = ['llamada','whatsapp','correo','reunion','otro']
  readonly resultados: { value: ResultadoSeguimiento; label: string }[] = [
    { value: 'no_contesto',   label: 'No contestó' },
    { value: 'contactado',    label: 'Contactado' },
    { value: 'interesado',    label: 'Interesado' },
    { value: 'no_interesado', label: 'No interesado' },
    { value: 'inscrito',      label: 'Inscrito' },
  ]

  ngOnInit(): void {
    this.campanaLeadId = Number(this.route.snapshot.paramMap.get('campanaId'))
    this.leadId        = Number(this.route.snapshot.paramMap.get('leadId'))
    this.cargarLead()
    this.cargarSeguimientos()
  }

  private cargarLead(): void {
    this.leadSvc.getAll(this.campanaLeadId, { pageIndex: 1, pageSize: 1 }).subscribe()
    // Cargamos el lead buscandolo en la lista
    this.isLoading.set(true)
    this.leadSvc.getById(this.campanaLeadId, this.leadId).subscribe({
      next: (lead) => {
        this.lead.set(lead)
        this.isLoading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.hasError.set(true)
        this.isLoading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  private cargarSeguimientos(): void {
    this.seguimSvc.getSeguimientos(this.campanaLeadId, this.leadId).subscribe({
      next: (data) => {
        this.seguimientos.set(data)
        this.cdr.detectChanges()
      },
    })
  }

  // === Modal seguimiento ===
  modalAbierto  = signal(false)
  guardando     = signal(false)
  nuevoTipo     = signal<TipoSeguimiento>('llamada')
  nuevoResult   = signal<ResultadoSeguimiento>('contactado')
  nuevaNota     = signal('')
  nuevaAccion   = signal('')
  nuevaFecha    = signal('')

  abrirModal(): void {
    this.nuevoTipo.set('llamada')
    this.nuevoResult.set('contactado')
    this.nuevaNota.set('')
    this.nuevaAccion.set('')
    this.nuevaFecha.set('')
    this.modalAbierto.set(true)
  }

  cerrarModal(): void {
    this.modalAbierto.set(false)
  }

  guardarSeguimiento(): void {
    this.guardando.set(true)
    const payload: CreateSeguimientoPayload = {
      tipo:                 this.nuevoTipo(),
      resultado:            this.nuevoResult(),
      nota:                 this.nuevaNota().trim() || null,
      proxima_accion:       this.nuevaAccion().trim() || null,
      fecha_proxima_accion: this.nuevaFecha() || null,
    }

    this.seguimSvc.crearSeguimiento(this.campanaLeadId, this.leadId, payload).subscribe({
      next: (nuevo) => {
        this.seguimientos.update(list => [nuevo, ...list])
        // Recargar lead para ver estado actualizado
        this.leadSvc.getById(this.campanaLeadId, this.leadId).subscribe({
          next: (l) => { this.lead.set(l); this.cdr.detectChanges() }
        })
        this.guardando.set(false)
        this.modalAbierto.set(false)
        this.toast.success('Listo', 'Seguimiento registrado.')
        this.cdr.detectChanges()
      },
      error: (err: HttpErrorResponse) => {
        this.guardando.set(false)
        this.toast.error('Error', err?.error?.message ?? 'No se pudo registrar el seguimiento.')
        this.cdr.detectChanges()
      }
    })
  }

  // === Cambiar estado manual ===
  cambiandoEstado = signal(false)

  cambiarEstado(estado: EstadoLead): void {
    const l = this.lead()
    if (!l || l.estado === estado) return
    this.cambiandoEstado.set(true)
    this.seguimSvc.actualizarEstado(this.campanaLeadId, this.leadId, { estado }).subscribe({
      next: (actualizado) => {
        this.lead.set(actualizado)
        this.cambiandoEstado.set(false)
        this.toast.success('Estado actualizado', 'El lead fue movido a: ' + ESTADO_LABEL[estado])
        this.cdr.detectChanges()
      },
      error: () => {
        this.cambiandoEstado.set(false)
        this.toast.error('Error', 'No se pudo cambiar el estado.')
        this.cdr.detectChanges()
      }
    })
  }

  formatFecha(iso: string | null): string {
    if (!iso) return ''
    return new Date(iso).toLocaleString('es-BO', { dateStyle: 'medium', timeStyle: 'short' })
  }

  capitalize(s: string): string {
    return s.charAt(0).toUpperCase() + s.slice(1)
  }
}
