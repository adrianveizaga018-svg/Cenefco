import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core'
import { CommonModule } from '@angular/common'
import { ActivatedRoute, RouterLink } from '@angular/router'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideLoader, lucidePlus, lucideWallet, lucideTarget, lucideEye, lucideCheckCircle, lucideUsers } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaPublicidadService } from '../../application/services/campana-publicidad.service'
import { CampanaPublicidad, FuenteMetrica } from '../../domain/models/campana-publicidad.model'
import { GastoService } from '../../../gastos/application/services/gasto.service'
import { Gasto } from '../../../gastos/domain/models/gasto.model'

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
  selector: 'app-campana-detail',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, NgIcon, PageTitle, SearchableSelect],
  viewProviders: [provideIcons({ lucideLoader, lucidePlus, lucideWallet, lucideTarget, lucideEye, lucideCheckCircle, lucideUsers })],
  templateUrl: './campana-detail.html',
})
export class CampanaDetail implements OnInit {
  private route = inject(ActivatedRoute)
  private svc   = inject(CampanaPublicidadService)
  private gastoSvc = inject(GastoService)
  private toast = inject(ToastService)
  private cdr   = inject(ChangeDetectorRef)

  campanaId = signal<number>(0)
  campana   = signal<CampanaPublicidad | null>(null)
  gastos    = signal<Gasto[]>([])
  loading   = signal(true)
  guardandoMetrica = signal(false)

  readonly fuenteOptions: SelectOption[] = [
    { value: 'manual', label: 'Carga manual' },
    { value: 'meta_ads_manager', label: 'Meta Ads Manager' },
    { value: 'google_ads', label: 'Google Ads' },
    { value: 'tiktok_ads', label: 'TikTok Ads' },
  ]

  fechaCorte         = signal(new Date().toISOString().substring(0, 10))
  alcance            = signal<number | null>(null)
  impresiones        = signal<number | null>(null)
  clicsEnlace        = signal<number | null>(null)
  resultados         = signal<number | null>(null)
  tipoResultado      = signal('')
  costoPorResultado  = signal<number | null>(null)
  gastoPeriodo       = signal<number | null>(null)
  fuente             = signal<FuenteMetrica>('manual')

  ngOnInit() {
    const id = Number(this.route.snapshot.paramMap.get('id'))
    this.campanaId.set(id)
    this.cargar()
  }

  plataformaLabel(p: string): string {
    return PLATAFORMA_LABELS[p] ?? p
  }

  estadoBadgeClass(e: string): string {
    return ESTADO_BADGE[e] ?? 'bg-default-200 text-default-600'
  }

  fuenteLabel(f: string): string {
    return this.fuenteOptions.find(o => o.value === f)?.label ?? f
  }

  private cargar() {
    this.loading.set(true)
    this.svc.getById(this.campanaId()).subscribe({
      next: (c) => {
        this.campana.set(c)
        this.cargarGastos()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la campaña.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  private cargarGastos() {
    this.gastoSvc.getAll({ campana_publicidad_id: this.campanaId(), pageSize: 50 }).subscribe({
      next: (res) => {
        this.gastos.set(res.data)
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  registrarMetrica() {
    if (!this.fechaCorte()) {
      this.toast.error('Datos incompletos', 'Indica la fecha de corte del reporte.')
      return
    }

    this.guardandoMetrica.set(true)

    this.svc.registrarMetrica(this.campanaId(), {
      fecha_corte:          this.fechaCorte(),
      alcance:              this.alcance(),
      impresiones:          this.impresiones(),
      clics_enlace:         this.clicsEnlace(),
      resultados:           this.resultados(),
      tipo_resultado:       this.tipoResultado() || null,
      costo_por_resultado:  this.costoPorResultado(),
      gasto_periodo:        this.gastoPeriodo(),
      fuente:               this.fuente(),
    }).subscribe({
      next: () => {
        this.toast.success('Registrado', 'Corte de métricas registrado correctamente.')
        this.guardandoMetrica.set(false)
        this.resetFormularioMetrica()
        this.cargar()
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? 'No se pudo registrar la métrica.')
        this.guardandoMetrica.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  private resetFormularioMetrica() {
    this.fechaCorte.set(new Date().toISOString().substring(0, 10))
    this.alcance.set(null)
    this.impresiones.set(null)
    this.clicsEnlace.set(null)
    this.resultados.set(null)
    this.tipoResultado.set('')
    this.costoPorResultado.set(null)
    this.gastoPeriodo.set(null)
    this.fuente.set('manual')
  }
}
