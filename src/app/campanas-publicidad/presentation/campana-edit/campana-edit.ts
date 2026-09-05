import { Component, inject, signal, computed, ChangeDetectorRef, OnInit } from '@angular/core'
import { ActivatedRoute, Router, RouterLink } from '@angular/router'
import { FormsModule } from '@angular/forms'
import { NgIcon, provideIcons } from '@ng-icons/core'
import { lucideSave, lucideLoader } from '@ng-icons/lucide'
import { PageTitle } from '../../../common/components/page-title/page-title'
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select'
import { ToastService } from '../../../common/application/services/toast.service'
import { CampanaPublicidadService } from '../../application/services/campana-publicidad.service'
import { CursoService } from '../../../cursos/application/services/curso.service'
import { Curso } from '../../../cursos/domain/models/curso.model'
import { PropositoCampana, PlataformaCampana, EstadoCampana } from '../../domain/models/campana-publicidad.model'

@Component({
  selector: 'app-campana-edit',
  standalone: true,
  imports: [FormsModule, NgIcon, PageTitle, SearchableSelect, RouterLink],
  viewProviders: [provideIcons({ lucideSave, lucideLoader })],
  templateUrl: './campana-edit.html',
})
export class CampanaEdit implements OnInit {
  private route    = inject(ActivatedRoute)
  private router   = inject(Router)
  private svc      = inject(CampanaPublicidadService)
  private cursoSvc = inject(CursoService)
  private toast    = inject(ToastService)
  private cdr      = inject(ChangeDetectorRef)

  isNew     = signal(true)
  campanaId = signal<number | null>(null)
  loading   = signal(true)
  saving    = signal(false)

  cursoOptions = signal<SelectOption[]>([])
  private cursos = signal<Curso[]>([])

  inscritosCurso = computed<number | null>(() => {
    const id = this.programaId()
    if (id == null) return null
    return this.cursos().find(c => c.id_programa === id)?.total_inscritos ?? null
  })

  readonly propositoOptions: SelectOption[] = [
    { value: 'curso', label: 'Curso / Programa específico' },
    { value: 'institucional', label: 'Institucional (marca CENEFCO)' },
    { value: 'evento', label: 'Evento' },
    { value: 'reclutamiento', label: 'Reclutamiento de docentes' },
    { value: 'otro', label: 'Otro' },
  ]

  readonly plataformaOptions: SelectOption[] = [
    { value: 'meta_ads', label: 'Meta Ads (Facebook/Instagram)' },
    { value: 'google_ads', label: 'Google Ads' },
    { value: 'tiktok_ads', label: 'TikTok Ads' },
    { value: 'otro', label: 'Otro' },
  ]

  readonly estadoOptions: SelectOption[] = [
    { value: 'planificada', label: 'Planificada' },
    { value: 'activa', label: 'Activa' },
    { value: 'pausada', label: 'Pausada' },
    { value: 'finalizada', label: 'Finalizada' },
    { value: 'cancelada', label: 'Cancelada' },
  ]

  proposito               = signal<PropositoCampana>('curso')
  programaId               = signal<number | null>(null)
  nombre                    = signal('')
  plataforma                = signal<PlataformaCampana>('meta_ads')
  objetivo                   = signal('')
  fechaInicio                = signal(new Date().toISOString().substring(0, 10))
  fechaFin                    = signal('')
  estado                       = signal<EstadoCampana>('planificada')
  leads                         = signal<number | null>(null)
  presupuestoUsd                 = signal<number | null>(null)
  presupuestoBob                   = signal<number | null>(null)
  totalGastado                       = signal<number>(0)
  idCampanaExterna                = signal('')
  responsable                      = signal('')
  notas                             = signal('')

  ngOnInit() {
    this.cargarCursos()

    const id = this.route.snapshot.paramMap.get('id')
    if (!id || id === 'new') {
      this.isNew.set(true)
      this.loading.set(false)
      this.cdr.detectChanges()
      return
    }

    this.isNew.set(false)
    const numId = Number(id)
    this.campanaId.set(numId)
    this.svc.getById(numId).subscribe({
      next: (c) => {
        this.proposito.set(c.proposito)
        this.programaId.set(c.programa_id)
        this.nombre.set(c.nombre)
        this.plataforma.set(c.plataforma)
        this.objetivo.set(c.objetivo ?? '')
        this.fechaInicio.set(c.fecha_inicio)
        this.fechaFin.set(c.fecha_fin ?? '')
        this.estado.set(c.estado)
        this.leads.set(c.leads)
        this.presupuestoUsd.set(c.presupuesto_usd)
        this.presupuestoBob.set(c.presupuesto_bob)
        this.totalGastado.set(c.total_gastado)
        this.idCampanaExterna.set(c.id_campana_externa ?? '')
        this.responsable.set(c.responsable ?? '')
        this.notas.set(c.notas ?? '')
        this.loading.set(false)
        this.cdr.detectChanges()
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar la campaña.')
        this.loading.set(false)
        this.cdr.detectChanges()
      }
    })
  }

  private cargarCursos() {
    this.cursoSvc.getAll({ pageSize: 100 }).subscribe({
      next: (res) => {
        this.cursos.set(res.data)
        this.cursoOptions.set(res.data.map(c => ({ value: c.id_programa, label: c.nombre_programa })))
        this.cdr.detectChanges()
      },
      error: () => {}
    })
  }

  save() {
    if (!this.nombre() || !this.plataforma() || !this.fechaInicio()) {
      this.toast.error('Datos incompletos', 'Completa nombre, plataforma y fecha de inicio antes de guardar.')
      return
    }

    this.saving.set(true)

    const payload = {
      nombre:                   this.nombre(),
      plataforma:               this.plataforma(),
      fecha_inicio:             this.fechaInicio(),
      programa_id:              this.proposito() === 'curso' ? this.programaId() : null,
      proposito:                this.proposito(),
      objetivo:                 this.objetivo() || null,
      fecha_fin:                this.fechaFin() || null,
      estado:                   this.estado(),
      leads:                    this.leads(),
      presupuesto_usd:          this.presupuestoUsd(),
      presupuesto_bob:          this.presupuestoBob(),
      id_campana_externa:       this.idCampanaExterna() || null,
      responsable:              this.responsable() || null,
      notas:                    this.notas() || null,
    }

    const request = this.isNew()
      ? this.svc.create(payload)
      : this.svc.update(this.campanaId()!, payload)

    request.subscribe({
      next: () => {
        this.saving.set(false)
        this.toast.success('Guardado', this.isNew() ? 'Campaña registrada correctamente.' : 'Campaña actualizada.')
        this.router.navigate(['/cenefco/campanas-publicidad'])
      },
      error: (err) => {
        this.toast.error('Error', err?.error?.message ?? err?.error?.error ?? 'No se pudo guardar la campaña.')
        this.saving.set(false)
        this.cdr.detectChanges()
      }
    })
  }
}
